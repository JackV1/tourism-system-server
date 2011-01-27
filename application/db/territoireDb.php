<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/territoireModele.php');
	
	final class territoireDb
	{
	
		const SQL_TERRITOIRE = "SELECT idTerritoire, libelle FROM sitTerritoire WHERE idTerritoire='%d'";
		const SQL_UPDATE_TERRITOIRE = "UPDATE sitTerritoire SET libelle='%s' WHERE idTerritoire='%d'";
		const SQL_COMMUNES_TERRITOIRE = "SELECT codeInsee, prive FROM sitTerritoireCommune WHERE idTerritoire='%d'";
		const SQL_THESAURII_TERRITOIRE = "SELECT t.codeThesaurus FROM sitTerritoireThesaurus tt, sitThesaurus t WHERE tt.idThesaurus=t.idThesaurus AND idTerritoire='%d'";
		const SQL_DELETE_COMMUNE = "DELETE FROM sitTerritoireCommune WHERE idTerritoire='%d' AND codeInsee='%d'";
		//const SQL_ADD_COMMUNE = "INSERT INTO sitTerritoireCommune (idTerritoire, codeInsee) VALUES('%d', '%d')";
		const SQL_ADD_COMMUNE = "REPLACE INTO sitTerritoireCommune (idTerritoire, codeInsee) VALUES('%d', '%s')";
		const SQL_UPDATE_VISIBILITE = "UPDATE sitTerritoireCommune SET prive='%s' WHERE idTerritoire='%d' AND codeInsee='%d'";
		const SQL_DELETE_THESAURUS = "DELETE FROM sitTerritoireThesaurus WHERE idTerritoire='%d' AND idThesaurus='%d'";
		const SQL_ADD_THESAURUS = "INSERT INTO sitTerritoireThesaurus (idTerritoire, idThesaurus) VALUES('%d', '%d')";
		const SQL_CREATE_TERRITOIRE = "INSERT INTO sitTerritoire (libelle) VALUES('%s')";
		const SQL_DELETE_TERRITOIRE = "DELETE FROM sitTerritoire WHERE idTerritoire='%d'";
		const SQL_TERRITOIRES = "SELECT idTerritoire FROM sitTerritoire";
		
		
		public static function getTerritoire($idTerritoire)
		{
			if (is_numeric($idTerritoire) === false)
			{
				throw new ApplicationException("L'identifiant de territoire n'est pas numérique");
			}
			$result = tsDatabase::getRow(self::SQL_TERRITOIRE, array($idTerritoire), DB_FAIL_ON_ERROR);
			$oTerritoire = new territoireModele();
			$oTerritoire -> setLibelle($result['libelle']);
			$oTerritoire -> setIdTerritoire($result['idTerritoire']);
			return $oTerritoire;
		}
		
		
		public static function getTerritoires()
		{
			$oTerritoireCollection = new territoireCollection();
			$territoires = tsDatabase::getRecords(self::SQL_TERRITOIRES, array());
			foreach($territoires as $idTerritoire)
			{
				$oTerritoireCollection[] = self::getTerritoire($idTerritoire);
			}
			return $oTerritoireCollection -> getCollection();
		}
		
		
		public static function createTerritoire($libelle)
		{
			return tsDatabase::insert(self::SQL_CREATE_TERRITOIRE, array($libelle));
		}
		
		
		
		public static function updateTerritoire(territoireModele $oTerritoire)
		{
			return tsDatabase::query(self::SQL_UPDATE_TERRITOIRE, array($oTerritoire -> libelle, $oTerritoire -> idTerritoire));
		}
		
		
		public static function getCommunesByTerritoire(territoireModele $oTerritoire)
		{
			$communeCollection = new communeCollection();
			$communes = tsDatabase::getRows(self::SQL_COMMUNES_TERRITOIRE, array($oTerritoire -> idTerritoire));
			foreach($communes as $commune)
			{
				
				$oCommune = communeDb::getCommune($commune['codeInsee']) -> getObject();
				$oCommune -> prive = $commune['prive'] == 'Y';
				$communeCollection[] = $oCommune;
			}
			return $communeCollection -> getCollection();
		}
		
		
		public static function deleteCommuneTerritoire(territoireModele $oTerritoire, communeModele $oCommune)
		{
			return tsDatabase::query(self::SQL_DELETE_COMMUNE, array($oTerritoire -> idTerritoire, $oCommune -> codeInsee));
		}
		
		
		public static function addCommuneTerritoire(territoireModele $oTerritoire, communeModele $oCommune)
		{
			return tsDatabase::query(self::SQL_ADD_COMMUNE, array($oTerritoire -> idTerritoire, $oCommune -> codeInsee));
		}
		
		
		public static function setCommuneTerritoirePrive(territoireModele $oTerritoire, communeModele $oCommune, $prive)
		{
			return tsDatabase::query(self::SQL_UPDATE_VISIBILITE, array($prive ? 'Y' : 'N', $oTerritoire -> idTerritoire, $oCommune -> codeInsee));
		}
		
		
		public static function getThesaurusByTerritoire(territoireModele $oTerritoire)
		{
			$thesaurusCollection = new thesaurusCollection();
			$thesaurii = tsDatabase::getRecords(self::SQL_THESAURII_TERRITOIRE, array($oTerritoire -> idTerritoire));
			foreach($thesaurii as $codeThesaurus)
			{
				$thesaurusCollection[] = thesaurusDb::getThesaurus($codeThesaurus) -> getObject();
			}
			return $thesaurusCollection -> getCollection();
		}
		
		
		public static function deleteThesaurusTerritoire(territoireModele $oTerritoire, thesaurusModele $oThesaurus)
		{
			return tsDatabase::query(self::SQL_DELETE_THESAURUS, array($oTerritoire -> idTerritoire, $oThesaurus -> idThesaurus));
		}
		
		
		public static function addThesaurusTerritoire(territoireModele $oTerritoire, thesaurusModele $oThesaurus)
		{
			return tsDatabase::query(self::SQL_ADD_THESAURUS, array($oTerritoire -> idTerritoire, $oThesaurus -> idThesaurus));
		}
		
		
		public static function deleteTerritoire(territoireModele $oTerritoire)
		{
			return tsDatabase::query(self::SQL_DELETE_TERRITOIRE, array($oTerritoire -> idTerritoire));
		}
		
	}
	
	
?>