<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/thesaurusModele.php');
	require_once('application/modele/entreeThesaurusModele.php');
	
	// @TODO : changer la clé primaire vers codeThesaurus
	final class thesaurusDb
	{
	
		const SQL_THESAURUS = "SELECT codeThesaurus, libelle, prefixe, idThesaurus FROM sitThesaurus WHERE codeThesaurus='%s'";
		const SQL_THESAURII = "SELECT codeThesaurus FROM sitThesaurus";
		const SQL_UPDATE_THESAURUS = 'UPDATE sitThesaurus SET codeThesaurus=\'%1$s\', libelle=\'%2$s\' WHERE codeThesaurus=\'%1$s\'';
		const SQL_CREATE_THESAURUS = "INSERT INTO sitThesaurus(codeThesaurus, libelle, prefixe) VALUES ('%s', '%s', '%s')";
		const SQL_ENTREES_THESAURUS = "SELECT cle, libelle, liste FROM sitEntreesThesaurus WHERE codeThesaurus='%s' AND lang='%s'";
		const SQL_ADD_ENTREE_THESAURUS = "INSERT INTO sitEntreesThesaurus (codeThesaurus, cle, liste, lang, libelle) VALUES ('%s', '%s', '%s', '%s', '%s')";
		//const SQL_LISTE_FROM_CLE = "SELECT liste FROM sitEntreesThesaurus WHERE cle='%s' AND codeThesaurus='MTH.NAT.TIFV30' AND lang='fr'";
		const SQL_LISTE_FROM_CLE = "SELECT liste FROM sitEntreesThesaurus WHERE cle='%s' AND lang='fr'";
		const SQL_NUMERO_TIF = "
		SELECT IF (MAX(SUBSTRING_INDEX(cle, '.', -1) + 1) < 10,
						CONCAT(SUBSTRING(cle, 1, LENGTH(cle) - LOCATE('.', REVERSE(cle))), '.', '0', MAX(SUBSTRING_INDEX(cle, '.', -1) + 1)), 
						CONCAT(SUBSTRING(cle, 1, LENGTH(cle) - LOCATE('.', REVERSE(cle))), '.', MAX(SUBSTRING_INDEX(cle, '.', -1) + 1)))
				AS codeTIF FROM sitEntreesThesaurus WHERE codeThesaurus='%s' AND (LOCATE('%s',cle)=4 OR LOCATE('%s',cle)=5)";
		const SQL_CODE_PARENT = "SELECT SUBSTRING_INDEX(cle, '.', -1)) AS numero FROM sitEntreesThesaurus WHERE codeThesaurus='%s' AND liste='%s'";
		const SQL_PREFIXE_THESAURUS = "SELECT MAX(prefixe) FROM sitThesaurus WHERE prefixe IS NOT NULL";
		const SQL_DELETE_ENTREE_THESAURUS = "DELETE FROM sitEntreesThesaurus WHERE cle='%s' AND codeThesaurus<>'MTH.NAT.TIFV30'";
		//const SQL_SET_ENTREE_THESAURUS = "UPDATE sitEntreesThesaurus SET libelle='%s' WHERE cle='%s' AND lang='%s'";
		const SQL_SET_ENTREE_THESAURUS = "REPLACE INTO sitEntreesThesaurus(libelle, cle, lang, codeThesaurus,liste) VALUES ('%s', '%s', '%s','%s','%s')";
		const SQL_VALUE_BY_KEY = "SELECT libelle FROM sitEntreesThesaurus WHERE cle='%s' AND lang='%s'";
		const SQL_THESAURUS_BY_KEY = "SELECT codeThesaurus FROM sitEntreesThesaurus WHERE cle='%s' LIMIT 0,1";
		const SQL_LISTE_THESAURUS = "SELECT cle, libelle FROM sitEntreesThesaurus WHERE liste = '%s' 
									AND cle REGEXP('^%s$') AND codeThesaurus = '%s' AND lang = 'fr'";
		//const SQL_ARBRE_THESAURUS = "SELECT cle, libelle FROM sitEntreesThesaurus WHERE cle REGEXP('^((99|[0-9]{3}).)?%s.[0-9]{2,3}$') AND codeThesaurus = '%s' AND lang = 'fr'";
		const SQL_ARBRE_THESAURUS = "SELECT cle, libelle FROM sitEntreesThesaurus WHERE cle REGEXP('^((99|[0-9]{3}).)?%s(.[0-9]{2,3})*$') AND codeThesaurus = '%s' AND lang = 'fr'";
									
		private static $pop;
		private static $entrees;
		
		
		public static function getThesaurus($codeThesaurus)
		{
			$result = tsDatabase::getObject(self::SQL_THESAURUS, array($codeThesaurus, DB_FAIL_ON_ERROR));
			$oThesaurus = thesaurusModele::getInstance($result, 'thesaurusModele');
			return $oThesaurus;
		}
		
		
		public static function createThesaurus($codeThesaurus, $libelle)
		{
			if (self::isCodeThesaurusValide($codeThesaurus) === false)
			{
				throw new ApplicationException("Le code thésaurus n'est pas valide");
			}
			$prefixe = self::getNextPrefixeThesaurus();
			tsDatabase::insert(self::SQL_CREATE_THESAURUS, array($codeThesaurus, $libelle, $prefixe));
			return $prefixe;
		}
		
		
		public static function updateThesaurus($oThesaurus, $codeThesaurus, $libelle)
		{
			if (self::isCodeThesaurusValide($codeThesaurus) === false)
			{
				throw new ApplicationException("Le code thésaurus $codeThesaurus n'est pas valide");
			}
			return tsDatabase::query(self::SQL_UPDATE_THESAURUS, array($codeThesaurus, $libelle));
		}
		
		
		public static function getThesaurii()
		{
			$oThesaurusCollection = new ThesaurusCollection();
			$thesaurii = tsDatabase::getRecords(self::SQL_THESAURII, array());
			foreach($thesaurii as $thesaurus)
			{
				$oThesaurusCollection[] = self::getThesaurus($thesaurus) -> getObject();
			}
			return $oThesaurusCollection -> getCollection();
		}
		
		
		public static function getEntreesThesaurus(thesaurusModele $oThesaurus, $codeLangue)
		{
			if (self::isCodeLangueValide($codeLangue) === false)
			{
				throw new ApplicationException("Le code langue n'est pas valide");
			}
			
			$oEntreeThesaurusCollection = new entreeThesaurusCollection();
			$entreesThesaurus = tsDatabase::getRows(self::SQL_ENTREES_THESAURUS, array($oThesaurus -> codeThesaurus, $codeLangue));
			foreach($entreesThesaurus as $entreeThesaurus)
			{
				$oEntreeThesaurus = new entreeThesaurusModele();
				$oEntreeThesaurus -> setCle($entreeThesaurus['cle']);
				$oEntreeThesaurus -> setLibelle($entreeThesaurus['libelle']);
				$oEntreeThesaurus -> setListe($entreeThesaurus['liste']);
				$oEntreeThesaurus -> setLang($codeLangue);
				$oEntreeThesaurusCollection[] = $oEntreeThesaurus -> getObject();
			}
			
			return $oEntreeThesaurusCollection -> getCollection();
		}
		
		
		public static function addEntreeThesaurus(thesaurusModele $oThesaurus, $cleParente, $libelle, $codeLangue)
		{
			
			if (self::isCodeLangueValide($codeLangue) === false)
			{
				throw new ApplicationException("Le code langue n'est pas valide");
			}
			
			$liste = self::getListeFromCle($cleParente);
			//$nextCode = tsDatabase::getRecord(self::SQL_NUMERO_TIF, array($oThesaurus -> codeThesaurus, $liste));
			$nextCode = tsDatabase::getRecord(self::SQL_NUMERO_TIF, array($oThesaurus -> codeThesaurus, $cleParente,$cleParente));
			if (is_null($nextCode))
			{
				$nextCode = $oThesaurus -> prefixe . '.' . $cleParente . '.01'; 
			}

			tsDatabase::insert(self::SQL_ADD_ENTREE_THESAURUS, array($oThesaurus -> codeThesaurus, $nextCode, $liste, $codeLangue, $libelle));
			return $nextCode;
		}
		
		
		public static function deleteEntreeThesaurus($codeTIF)
		{
			return tsDatabase::query(self::SQL_DELETE_ENTREE_THESAURUS, array($codeTIF));
		}
		
		
		
		public static function setEntreeThesaurus($codeTIF, $codeLangue, $libelle)
		{
			if (self::isCodeLangueValide($codeLangue) === false)
			{
				throw new ApplicationException("Le code langue n'est pas valide");
			}
			$codeThesaurus = self::getThesaurusByKey($codeTIF);
			$liste = self::getListeFromCle($codeTIF,false);
			return tsDatabase::query(self::SQL_SET_ENTREE_THESAURUS, array($libelle, $codeTIF, $codeLangue,$codeThesaurus,$liste));
		}
		
		
		public static function getListeFromCle($cle, $cleParente = true)
		{
			// Jermey
			//$liste = tsDatabase::getRecord(self::SQL_LISTE_FROM_CLE, array($cle . '.01'));
			$liste = tsDatabase::getRecord(self::SQL_LISTE_FROM_CLE, array($cle . ($cleParente ? '.01' : '')));
			// Jermey
			if (is_null($liste))
			{
				throw new ApplicationException("Le code TIF fourni n'est pas valide");
			}
			return $liste;
		}
		
		
		public static function getValueByKey($cle, $codeLangue = 'fr')
		{
			if (self::isCodeLangueValide($codeLangue) === false)
			{
				throw new ApplicationException("Le code langue n'est pas valide");
			}
			$value = tsDatabase::getRecord(self::SQL_VALUE_BY_KEY, array($cle, $codeLangue), DB_FAIL_ON_ERROR);
			
			if (is_null($value))
			{
				throw new ApplicationException("Le code TIF fourni n'est pas valide");
			}
			return $value;
		}
		
		
		
		// Jermey
		public static function getListeThesaurus($liste, $cle, $pop)
		{
			$cle = str_replace('.*', '', $cle);
			$cle = ($cle == '' ? '.*' : "((99|[0-9]{3}).)?$cle(.[0-9]{2,3})+");
			
			self::$pop = ($pop != '' ? explode(',', $pop) : array());
			
			if (tsDroits::isRoot() === false)
			{
				$idGroupe = tsDroits::getGroupeUtilisateur();
				$oGroupe = groupeDb::getGroupe($idGroupe);
				$oTerritoires = groupeDb::getGroupeTerritoires($oGroupe);
			}
			else
			{
				$oTerritoires = array();
			}
			
			$result = array();
			$resultTmp = tsDatabase::getRows(self::SQL_LISTE_THESAURUS, array($liste, $cle, 'MTH.NAT.TIFV30'), DB_FAIL_ON_ERROR);
			$resultTmp = array_filter($resultTmp, array('self', 'popListeThesaurus'));
			$result = array_merge($result, $resultTmp);
			$result = array_merge($result, tsDatabase::getRows(self::SQL_LISTE_THESAURUS, array($liste, $cle, 'MTH.LOC.RAC'), DB_FAIL_ON_ERROR));
			foreach ($oTerritoires as $oTerritoire)
			{
				$oThesaurii = territoireDb::getThesaurusByTerritoire($oTerritoire);
				foreach ($oThesaurii as $oThesaurus)
				{
					$result = array_merge($result, tsDatabase::getRows(self::SQL_LISTE_THESAURUS, array($liste, $cle, $oThesaurus -> codeThesaurus), DB_FAIL_ON_ERROR));
				}
			}
			
			return $result;
		}
		
		
		
		public static function getArbreThesaurus($cle, $pop)
		{
			$cle = str_replace('.*', '', $cle);
			
			if ($cle == '')
			{
				throw new ApplicationException("Code TIF invalide");
			}
			
			self::$pop = ($pop != '' ? explode(',', $pop) : array());
			
			if (tsDroits::isRoot() === false)
			{
				$idGroupe = tsDroits::getGroupeUtilisateur();
				$oGroupe = groupeDb::getGroupe($idGroupe);
				$oTerritoires = groupeDb::getGroupeTerritoires($oGroupe);
			}
			else
			{
				$oTerritoires = array();
			}
			
			$entrees = tsDatabase::getRows(self::SQL_ARBRE_THESAURUS, array($cle, 'MTH.NAT.TIFV30'), DB_FAIL_ON_ERROR);
			$entrees = array_merge($entrees, tsDatabase::getRows(self::SQL_ARBRE_THESAURUS, array($cle, 'MTH.LOC.RAC'), DB_FAIL_ON_ERROR));
			foreach ($oTerritoires as $oTerritoire)
			{
				$oThesaurii = territoireDb::getThesaurusByTerritoire($oTerritoire);
				foreach ($oThesaurii as $oThesaurus)
				{
					$entrees = array_merge($entrees, tsDatabase::getRows(self::SQL_ARBRE_THESAURUS, array($cle, $oThesaurus -> codeThesaurus), DB_FAIL_ON_ERROR));
				}
			}
			self::$entrees = array_filter($entrees, array('self', 'popListeThesaurus'));
			
			$result = self::getEntreesRecursive($cle);
			
			return $result;
		}
		
		
		private static function getEntreesRecursive($cle)
		{
			$result = array();
			foreach (self::$entrees as $entree)
			{
				if (preg_match('/^([0-9]{2,3}.)?'.$cle.'.[0-9]{2,3}$/', $entree['cle']))
				{
					$resultTmp = $entree;
					
					$children = self::getEntreesRecursive($resultTmp['cle']);
					if (count($children) > 0)
					{
						$resultTmp['children'] = $children;
					}
					$result[] = $resultTmp;
				}
			}
			return $result;
		}
		
		
		private static function popListeThesaurus($item)
		{
			return !in_array($item['cle'], self::$pop);
		}
		
		
		private static function getNextPrefixeThesaurus()
		{
			$prefixe = tsDatabase::getRecord(self::SQL_PREFIXE_THESAURUS, array());
			return (is_null($prefixe) ? tsConfig::get('TS_THESAURUS_PREFIXE') : intval($prefixe) + 1);
		}
		
		
		private static function isCodeThesaurusValide($codeThesaurus)
		{
			return (preg_match("/^MTH\.LOC\.[A-Z]{3}([A-Z0-9]{1,3})?$/i", $codeThesaurus) == 1);
		}


		private static function isCodeLangueValide($codeLangue)
		{
			// @TODO : à déplacer dans un futur proche
			return in_array($codeLangue, array('fr', 'en', 'de', 'es', 'nl', 'it'));
		}
		
		
		private static function getThesaurusByKey($cle)
		{
			//Aller chercher le thésaurus dans la base
			$value = tsDatabase::getRecord(self::SQL_THESAURUS_BY_KEY, array($cle), DB_FAIL_ON_ERROR);
			return $value;
		}
	}
	
	
?>