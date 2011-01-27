<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/ficheModele.php');
	require_once('application/modele/ficheSimpleModele.php');
	require_once('application/utils/tsDOMDocument.php');
	require_once('application/utils/tifTools.php'); //#Anthony
	
	final class ficheDb
	{
	
		const SQL_FICHE_ID = "SELECT idFiche, raisonSociale, codeTIF, codeInsee, bordereau, gpsLat, gpsLng, publication, referenceExterne FROM sitFiche WHERE idFiche='%d'"; //#Anthony, publication
		const SQL_FICHE_CODETIF = "SELECT idFiche FROM sitFiche WHERE codeTIF='%s'";
		const SQL_FICHE_REFEXTERNE = "SELECT idFiche FROM sitFiche WHERE referenceExterne='%s'"; //#Anthony
		const SQL_FICHE_VERSION = "SELECT idFicheVersion, dateVersion, etat, xmlTIF FROM sitFicheVersion WHERE idFiche='%d' ORDER BY idFicheVersion DESC LIMIT 0,1";
		const SQL_FICHE_VERSION_ETAT = " AND fv.etat='%s' ORDER BY idFicheVersion DESC LIMIT 0,1";
		const SQL_FICHE_VERSION_IDVERSION = " AND fv.idFicheVersion='%d'";
		// @todo : Récupération des champs spécifiques via droits champs sur bordereau
		const SQL_FICHE_OBJET = "SELECT * FROM sitChamp WHERE FIND_IN_SET('%s', bordereau)>0";// AND xpath NOT LIKE '%%tif:ChampsSpecifiques%%'";
		const SQL_CREATE_FICHE = "INSERT INTO sitFiche (codeInsee, bordereau, gpsLat, gpsLng) VALUES ('%s', '%s', '%s', '%s')";
		const SQL_CREATE_FICHE_REFEXTERNE = "UPDATE sitFiche SET referenceExterne='%s' WHERE idFiche='%d'"; //#Anthony
		const SQL_CREATE_FICHE_CODETIF = "UPDATE sitFiche SET codeTIF='%s' WHERE idFiche='%d'";
		const SQL_DELETE_FICHE = "DELETE FROM sitFiche WHERE idFiche='%d'";
		const SQL_UPDATE_FICHE_VERSION = "INSERT INTO sitFicheVersion (idFicheVersion, idFiche, dateVersion, etat, dateValidation, xmlTIF) VALUES ('%d', '%d', NOW(), '%s', NOW(), '%s')";
		const SQL_UPDATE_FICHE_UTILISATEUR = "UPDATE sitFicheVersion SET idUtilisateur='%d' WHERE idFiche='%d' AND idFicheVersion='%d'";
		const SQL_UPDATE_RAISON_SOCIALE = "UPDATE sitFiche SET raisonSociale='%s' WHERE idFiche='%d'";
		const SQL_UPDATE_FICHE_GROUPE = "UPDATE sitFiche SET idGroupe='%d' WHERE idFiche='%d'";
		const SQL_UPDATE_PUBLICATION = "UPDATE sitFiche SET publication='%s' WHERE idFiche='%d'"; //#Anthony
		const SQL_UPDATE_GPS = "UPDATE sitFiche SET gpsLat='%s', gpsLng='%s' WHERE idFiche='%d'";
		
		
		
		
		
		public static function createFiche($bordereau, $codeInsee, $referenceExterne = null)//#Anthony
		{
			$oCommune = communeDb::getCommune($codeInsee);
			
			$idFiche = tsDatabase::insert(self::SQL_CREATE_FICHE, 
								array($codeInsee, $bordereau, $oCommune -> gpsLat, $oCommune -> gpsLng));
			
			if(is_null($referenceExterne) === false)
			{
				tsDatabase::query(self::SQL_CREATE_FICHE_REFEXTERNE, array($referenceExterne, $idFiche));
			}
			
			
			$codeTIF = self::getCodeTif($idFiche, $bordereau, $codeInsee);
			tsDatabase::query(self::SQL_CREATE_FICHE_CODETIF, array($codeTIF, $idFiche));
			
			// Création du xml
			$strXml = file_get_contents(tsConfig::get('TS_PATH_EMPTYXML'));

			$strXml = str_replace('{identifier}', $codeTIF, $strXml);
			$strXml = str_replace('{created}', date("Y-m-d"), $strXml);
			$strXml = str_replace('{modified}', date("Y-m-d"), $strXml);
			$strXml = str_replace('{code_postal}', $oCommune -> codePostal, $strXml);
			$strXml = str_replace('{code_commune}', $codeInsee, $strXml);
			$strXml = str_replace('{commune}', $oCommune -> libelle, $strXml);
			$strXml = str_replace('{gpsLat}', $oCommune -> gpsLat, $strXml);
			$strXml = str_replace('{gpsLng}', $oCommune -> gpsLng, $strXml);
			
			$infosBordereau = tifTools::getInfosBordereau($bordereau); //#Anthony
			$strXml = str_replace('{cleClassification}', $infosBordereau['cle'], $strXml); //#Anthony
			$strXml = str_replace('{libelleClassification}', $infosBordereau['libelle'], $strXml); //#Anthony
			
			tsDatabase::query(self::SQL_UPDATE_FICHE_VERSION, array('1', $idFiche, 'accepte', $strXml));
			
			if (tsDroits::isRoot() === false)
			{
				tsDatabase::query(self::SQL_UPDATE_FICHE_UTILISATEUR, array(tsDroits::getIdUtilisateur(), $idFiche, '1'));
				tsDatabase::query(self::SQL_UPDATE_FICHE_GROUPE, array(tsDroits::getGroupeUtilisateur(), $idFiche));
			}
			
			return $idFiche;
		}

		
		/**
		 * 
		 * @param object $codeTIF
		 * @return 
		 */
		public static function getFicheByCodeTIF($codeTIF)
		{
			$idFiche = tsDatabase::getRecord(self::SQL_FICHE_CODETIF, array($codeTIF), DB_FAIL_ON_ERROR);
			return self::getFicheByIdFiche($idFiche);
		}
		
		public static function getIdFicheByRefExterne($refExterne) //#Anthony
		{
			$idFiche = tsDatabase::getRecord(self::SQL_FICHE_REFEXTERNE, array($refExterne), DB_FAIL_ON_ERROR);
			return $idFiche;
		}
		
		
		public static function deleteFiche($oFiche)
		{
			return tsDatabase::query(self::SQL_DELETE_FICHE, array($oFiche -> idFiche));
		}
		
		
		public static function getFiches()
		{
			$oFiches = new ficheSimpleCollection();
			$idsFiche = tsDroits::getFichesAdministrables();
			foreach($idsFiche as $idFiche)
			{
				$oFiche = self::getFicheSimpleByIdFiche($idFiche);
				$oFiches[] = $oFiche -> getObject();
			}
			return $oFiches -> getCollection();
		}
		
		
		public static function getFicheByIdFiche($idFiche)
		{
			$fiche = tsDatabase::getRow(self::SQL_FICHE_ID, array($idFiche), DB_FAIL_ON_ERROR);
			
			$oFiche = new ficheModele();
			
			$oFiche -> setIdFiche($fiche['idFiche']);
			$oFiche -> setRaisonSociale($fiche['raisonSociale']);
			$oFiche -> setCodeTIF($fiche['codeTIF']);
			$oFiche -> setCodeInsee($fiche['codeInsee']);
			$oFiche -> setBordereau($fiche['bordereau']);
			$oFiche -> setGpsLat($fiche['gpsLat']);
			$oFiche -> setGpsLng($fiche['gpsLng']);
			$oFiche -> setPublication($fiche['publication']); //#Anthony
			
			
			$version = self::getFicheVersion($idFiche);
			$oFiche -> setIdVersion($version['idFicheVersion']);
			$oFiche -> setDateVersion($version['dateVersion']);
			$oFiche -> setEtatVersion($version['etat']);
			$oFiche -> setXml($version['xmlTIF']);
			
			// @hook getFiche
		//	tsHook::hookObject('getFiche', $oFiche);

			return $oFiche -> getObject();
		}
		
		
		
		public static function getFicheSimpleByIdFiche($idFiche)
		{
			$fiche = tsDatabase::getRow(self::SQL_FICHE_ID, array($idFiche), DB_FAIL_ON_ERROR);
			
			$oFiche = new ficheSimpleModele();
			
			$oFiche -> setIdFiche($fiche['idFiche']);
			$oFiche -> setRaisonSociale($fiche['raisonSociale']);
			$oFiche -> setCodeTIF($fiche['codeTIF']);
			$oFiche -> setCodeInsee($fiche['codeInsee']);
			$oFiche -> setBordereau($fiche['bordereau']);
			$oFiche -> setGpsLat($fiche['gpsLat']);
			$oFiche -> setGpsLng($fiche['gpsLng']);	
			$oFiche -> setPublication($fiche['publication']); //#Anthony
			$oFiche -> setReferenceExterne($fiche['referenceExterne']);
			
			$version = self::getFicheVersion($idFiche);
			$oFiche -> setDateVersion($version['dateVersion']);
			
			return $oFiche;
		}
		
		
		
		public static function sauvegardeFicheBrouillon($oFiche, $stdFiche)
		{
			self::sauvegardeFiche($oFiche, $stdFiche, true);
		}
		
		
		
		public static function sauvegardeFiche(ficheModele $oFiche, $stdFiche, $droitFiche, $brouillon = false)
		{
			$domFiche = new tsDOMDocument('1.0');
			$domFiche -> loadXML($oFiche -> xml);
			$domXpath = new DOMXpath($domFiche);
			$champs = tsDatabase::getRows(self::SQL_FICHE_OBJET, array($oFiche -> bordereau));
			
			foreach($champs as $champ)
			{
				if (isset($stdFiche[$champ['identifiant']]))
				{
					$oChamp = champDb::getChamp($champ['idChamp']);
					
					if ((tsDroits::getDroitFicheChamp($oFiche, $oChamp, $droitFiche) & DROIT_MODIFICATION) > 0)
					{
						$oldValue = libXml::getXpathValue($domXpath, $oChamp);
						$newValue = $stdFiche[$champ['identifiant']];
						
						// Noeud complexe
						if (is_array($oChamp -> champs) && count($oChamp -> champs) > 0)
						{
							$oldValue = storePrepare($oldValue, $oChamp -> identifiant);
							
							if (is_array($newValue) === false)
							{
								$newValue = json_decode($newValue, true);
							}
							$newValue = storePrepare($newValue, $oChamp -> identifiant);
							
							$toAdd = $newValue;
							$toDelete = $oldValue;
							
							storeCompare($toDelete, $toAdd);
							
							if (count($toDelete) > 0 || count($toAdd) > 0)
							{
								$domFiche = libXml::JSONtoXML($domFiche, $oChamp, $newValue);
								$domFiche -> saveXML();
							}
						}
						// Noeud simple
						else
						{
							if ($oldValue != $newValue)
							{
								$domFiche -> setValueFromXPath($oChamp -> xPath, $newValue);
								$domFiche -> saveXML();
							}
						}
					}
					
				}
				
			}

			$xml = $domFiche -> saveXML();

			// A CHANGER
			if (isset($stdFiche['raison_sociale']))
			{
				tsDatabase::query(self::SQL_UPDATE_RAISON_SOCIALE, array($stdFiche['raison_sociale'], $oFiche -> idFiche));
			}
			
			if (isset($stdFiche['gps_lat']) && isset($stdFiche['gps_lat']))
			{
				tsDatabase::query(self::SQL_UPDATE_GPS, array($stdFiche['gps_lat'], $stdFiche['gps_lng'], $oFiche -> idFiche));
			}
			return tsDatabase::query(self::SQL_UPDATE_FICHE_VERSION, array(($oFiche -> idVersion) + 1, $oFiche -> idFiche, 'accepte', $xml));
		}
		
		
		
		
		public static function sauvegardeFicheXml($idFiche, $xmlTif, $raisonSociale = false, $gpsLat = false, $gpsLng = false) //#Anthony
		{
			$oFiche = self::getFicheByIdFiche($idFiche);
			
			if ($raisonSociale)
			{
				tsDatabase::query(self::SQL_UPDATE_RAISON_SOCIALE, array($raisonSociale, $oFiche -> idFiche));
			}
			
			if ($gpsLat && $gpsLng)
			{
				tsDatabase::query(self::SQL_UPDATE_GPS, array($gpsLat, $gpsLng, $oFiche -> idFiche));
			}
			
			return tsDatabase::query(self::SQL_UPDATE_FICHE_VERSION, array(($oFiche -> idVersion) + 1, $oFiche -> idFiche, 'accepte', $xmlTif));	
		}
		
		
		
		
		public static function getFiche(ficheModele $oFiche, $droitFiche)
		{
			$champs = tsDatabase::getRows(self::SQL_FICHE_OBJET, array($oFiche -> bordereau));
			
			$oFiche -> editable = array();
			$oFiche -> readable = array();
			
			$domFiche = new DOMDocument('1.0');
			$domFiche -> loadXML($oFiche -> xml);
			$domXpath = new DOMXpath($domFiche);
			
			foreach($champs as $champ)
			{
				$oChamp = champDb::getChamp($champ['idChamp']);
				$droitChamp = tsDroits::getDroitFicheChamp($oFiche, $oChamp, $droitFiche);
				
				if ($droitChamp & DROIT_MODIFICATION)
				{
					$oFiche -> editable[$champ['identifiant']] = libXml::getXpathValue($domXpath, $oChamp);
				}
				elseif ($droitChamp & DROIT_VISUALISATION)
				{
					$oFiche -> readable[$champ['identifiant']] = libXml::getXpathValue($domXpath, $oChamp);
				}
			}
			
			return $oFiche;
		}
		
		
		
		
		public static function isXMLValide($xml)
		{
			// @todo
		}
		
		public static function setPublicationFiche($oFiche, $publication)
		{
			$publicationYN = ($publication === true) ? 'Y' : 'N';
			tsDatabase::query(self::SQL_UPDATE_PUBLICATION, array($publicationYN, $oFiche -> idFiche));
		}
		
		
		private static function getFicheVersionByEtat($idFiche, $etat = 'accepte')
		{
			$version = tsDatabase::getRow(self::SQL_FICHE_VERSION . self::SQL_FICHE_VERSION_ETAT, array($idFiche, $etat));
			if ($version === false)
			{
				throw new ApplicationException("La version demandée de la fiche $idFiche n'existe pas");
			}
			return $version;
		}
		
		
		private static function getCodeTif($idFiche, $bordereau, $codeInsee)
		{
			return(strtoupper($bordereau) . tifTools::getCodeRegionByCodeInsee($codeInsee) .
						'0' . mb_substr($codeInsee, 0, 2, 'UTF-8') . tsConfig::get('TS_NUMERO_BASE') .
						str_repeat('0', 6 - strlen($idFiche)) . $idFiche);
		}
		
		
		private static function getFicheVersion($idFiche, $idVersion = null)
		{
			$sqlFicheVersion = (is_null($idVersion)) ? self::SQL_FICHE_VERSION : self::SQL_FICHE_VERSION . self::SQL_FICHE_VERSION_IDVERSION;
			return(tsDatabase::getRow($sqlFicheVersion, array($idFiche, $idVersion)));
		}
		
		
		private static function checkDroitFiche($idFiche)
		{
			if (tsDroits::isFicheAdministrable($idFiche) === false)
			{
				throw new ApplicationException("La fiche $idFiche n'est pas administrable");
			}
		}

	}
	
	
?>