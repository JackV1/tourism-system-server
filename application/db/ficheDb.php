<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/ficheModele.php');
	require_once('application/modele/ficheSimpleModele.php');
	require_once('application/modele/versionModele.php');
	require_once('application/utils/tsDOMDocument.php');
	require_once('application/utils/tifTools.php');
	
	final class ficheDb
	{
		
		const SQL_FICHE_ID = "SELECT idFiche, raisonSociale, codeTIF, codeInsee, bordereau, gpsLat, gpsLng, publication, referenceExterne FROM sitFiche WHERE idFiche='%d'";
		const SQL_FICHE_CODETIF = "SELECT idFiche FROM sitFiche WHERE codeTIF='%s'";
		const SQL_FICHE_REFEXTERNE = "SELECT idFiche FROM sitFiche WHERE referenceExterne='%s'";
		
		const SQL_CREATE_FICHE = "INSERT INTO sitFiche (codeInsee, bordereau, gpsLat, gpsLng) VALUES ('%s', '%s', '%s', '%s')";
		const SQL_CREATE_FICHE_REFEXTERNE = "UPDATE sitFiche SET referenceExterne='%s' WHERE idFiche='%d'";
		const SQL_CREATE_FICHE_CODETIF = "UPDATE sitFiche SET codeTIF='%s' WHERE idFiche='%d'";
		const SQL_CREATE_FICHE_GROUPE = "UPDATE sitFiche SET idGroupe='%d' WHERE idFiche='%d'";
		
		const SQL_UPDATE_RAISON_SOCIALE = "UPDATE sitFiche SET raisonSociale='%s' WHERE idFiche='%d'";
		const SQL_UPDATE_GPS = "UPDATE sitFiche SET gpsLat='%s', gpsLng='%s' WHERE idFiche='%d'";
		const SQL_UPDATE_PUBLICATION = "UPDATE sitFiche SET publication='%s' WHERE idFiche='%d'";
		
		const SQL_DELETE_FICHE = "DELETE FROM sitFiche WHERE idFiche='%d'";
		
		const SQL_VERSIONS_FICHE = "SELECT idFicheVersion, dateVersion, idUtilisateur, etat, dateValidation FROM sitFicheVersion WHERE idFiche = '%d'";
		
		const SQL_FICHE_VERSION = "SELECT idFicheVersion, dateVersion, etat FROM sitFicheVersion WHERE idFiche='%d'";
		const SQL_FICHE_VERSION_ETAT = " AND etat='%s'";
		const SQL_FICHE_VERSION_IDVERSION = " AND idFicheVersion='%d'";
		const SQL_FICHE_VERSION_LASTVERSION = " ORDER BY idFicheVersion DESC LIMIT 0,1";
		
		const SQL_UPDATE_FICHE_VERSION = "INSERT INTO sitFicheVersion (idFicheVersion, idFiche, dateVersion, etat, dateValidation) VALUES ('%d', '%d', NOW(), '%s', NOW())";
		const SQL_UPDATE_FICHE_UTILISATEUR = "UPDATE sitFicheVersion SET idUtilisateur='%d' WHERE idFiche='%d' AND idFicheVersion='%d'";
		
		const SQL_DELETE_FICHE_VERSION = "DELETE FROM sitFicheVersion WHERE idFiche='%d' AND idFicheVersion = '%d'";
		
		// @todo : Récupération des champs spécifiques via droits champs sur bordereau
		const SQL_FICHE_OBJET = "SELECT * FROM sitChamp WHERE FIND_IN_SET('%s', bordereau)>0";// AND xpath NOT LIKE '%%tif:ChampsSpecifiques%%'";
		
		
		public static function createFiche($bordereau, $codeInsee, $referenceExterne = null)
		{
			$oCommune = communeDb::getCommune($codeInsee);
			
			if (is_null($oCommune))
			{
				throw new ApplicationException("Commune introuvable : " . $codeInsee);
			}
			
			$idFiche = tsDatabase::insert(self::SQL_CREATE_FICHE, 
						array($codeInsee, $bordereau, $oCommune -> gpsLat, $oCommune -> gpsLng));
			
			if(is_null($referenceExterne) === false)
			{
				tsDatabase::query(self::SQL_CREATE_FICHE_REFEXTERNE, array($referenceExterne, $idFiche));
			}
			
			$codeTIF = self::getCodeTif($idFiche, $bordereau, $codeInsee);
			tsDatabase::query(self::SQL_CREATE_FICHE_CODETIF, array($codeTIF, $idFiche));
			
			if (tsDroits::isRoot() === false)
			{
				tsDatabase::query(self::SQL_CREATE_FICHE_GROUPE, array(tsDroits::getGroupeUtilisateur(), $idFiche));
			}
			
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
			
			$infosBordereau = tifTools::getInfosBordereau($bordereau);
			$strXml = str_replace('{cleClassification}', $infosBordereau['cle'], $strXml);
			$strXml = str_replace('{libelleClassification}', $infosBordereau['libelle'], $strXml);
			
			self::createFicheVersion($idFiche, $strXml);
			
			return $idFiche;
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
		
		
		
		public static function getFicheByIdFiche($idFiche, $idFicheVersion = null)
		{
			$fiche = tsDatabase::getObject(self::SQL_FICHE_ID, array($idFiche), DB_FAIL_ON_ERROR);
			$oFiche = ficheModele::getInstance($fiche, 'ficheModele');
			
			$version = self::getFicheVersion($idFiche, $idFicheVersion);
			$oFiche -> setIdVersion($version['idFicheVersion']);
			$oFiche -> setDateVersion($version['dateVersion']);
			$oFiche -> setEtatVersion($version['etat']);
			$oFiche -> setXml($version['xmlTIF']);
			
			// @hook getFiche
			// tsHook::hookObject('getFiche', $oFiche);
			
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
			$oFiche -> setPublication($fiche['publication']);
			$oFiche -> setReferenceExterne($fiche['referenceExterne']);
			
			$version = self::getFicheVersion($idFiche);
			$oFiche -> setDateVersion($version['dateVersion']);
			
			return $oFiche;
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

		
		
		public static function getIdFicheByCodeTIF($codeTIF)
		{
			return tsDatabase::getRecord(self::SQL_FICHE_CODETIF, array($codeTIF), DB_FAIL_ON_ERROR);
		}
		
		
		
		public static function getIdFicheByRefExterne($refExterne)
		{
			return tsDatabase::getRecord(self::SQL_FICHE_REFEXTERNE, array($refExterne), DB_FAIL_ON_ERROR);
		}
		
		
		
		public static function sauvegardeFicheBrouillon($oFiche, $stdFiche)
		{
			return self::sauvegardeFiche($oFiche, $stdFiche, true);
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
			
			return self::createFicheVersion($oFiche -> idFiche, $xml);
		}
		
		
		
		public static function setPublicationFiche($oFiche, $publication)
		{
			$publicationYN = ($publication === true) ? 'Y' : 'N';
			return tsDatabase::query(self::SQL_UPDATE_PUBLICATION, array($publicationYN, $oFiche -> idFiche));
		}
		
		
		
		public static function deleteFiche($oFiche)
		{
			$path = self::getPathByIdFiche($oFiche -> idFiche);
			$pathArchives = tsConfig::get('TS_PATH_ARCHIVES_XML');
			
			// Archivage
			$lastVersion = self::getFicheVersion($oFiche -> idFiche);
			$result = copy($path . $oFiche -> idFiche . '-' . $lastVersion['idFicheVersion'] . '.xml', $pathArchives . $oFiche -> idFiche . '.xml');
			if ($result === false)
			{
				throw new Exception("Impossible d'archiver la fiche : " . $oFiche -> idFiche);
			}
			
			$dir = opendir($path);
			if ($dir !== false)
			{
				while($entry = readdir($dir))
				{
					if (!is_dir($path.$entry) && $entry != '.' && $entry != '..')
					{
						unlink($path.$entry);
					}
				}
				closedir($dir);
			}
			rmdir($path);
			
			return tsDatabase::query(self::SQL_DELETE_FICHE, array($oFiche -> idFiche));
		}
		
		
		
		public function createFicheVersion($idFiche, $xml)
		{
			$lastVersion = self::getFicheVersion($idFiche);
			$newIdVersion = (is_null($lastVersion) === false ? $lastVersion['idFicheVersion'] + 1 : 1);
			
			// Ne pas sauvegarder si l'ancienne version est la même
			if ($newIdVersion > 1 && $lastVersion['xmlTIF'] == $xml)
			{
				return true;
			}
			
			$path = self::getPathByIdFiche($idFiche);
			
			$xmlFile = $path . $idFiche . '-' . $newIdVersion . '.xml';
			$result = file_put_contents($xmlFile, $xml);
			if ($result === false)
			{
				throw new Exception("Impossible de créer la nouvelle version de la fiche : $idFiche");
			}
			
			$xmlFile = $path . $idFiche . '.xml';
			$result = file_put_contents($xmlFile, $xml);
			if ($result === false)
			{
				throw new Exception("Impossible de créer la nouvelle version de la fiche : $idFiche");
			}
			
			$result = tsDatabase::query(self::SQL_UPDATE_FICHE_VERSION, array($newIdVersion, $idFiche, 'accepte'));
			
			if (tsDroits::isRoot() === false)
			{
				tsDatabase::query(self::SQL_UPDATE_FICHE_UTILISATEUR, array(tsDroits::getIdUtilisateur(), $idFiche, $newIdVersion));
			}
			
			self::updateSitFiche($idFiche);
			
			return $result;
		}
		
		
		
		public function getFicheVersions(ficheModele $oFiche)
		{
			$oVersionCollection = new versionCollection();
			$versions = tsDatabase::getRows(self::SQL_VERSIONS_FICHE, array($oFiche -> idFiche));
			foreach($versions as $version)
			{
				$oVersion = new versionModele();
				$oVersion -> setIdFicheVersion($version['idFicheVersion']);
				$oVersion -> setIdFiche($oFiche -> idFiche);
				$oVersion -> setDateVersion($version['dateVersion']);
				$oVersion -> setIdUtilisateur($version['idUtilisateur']);
				$oVersion -> setEtat($version['etat']);
				$oVersion -> setDateValidation($version['dateValidation']);
				$oVersionCollection[] = $oVersion -> getObject();
			}
			return $oVersionCollection -> getCollection();
		}
		
		
		
		public function deleteFicheVersion($oFiche, $idFicheVersion = null)
		{
			$path = self::getPathByIdFiche($oFiche -> idFiche);
			
			if (is_null($idFicheVersion))
			{
				$lastVersion = self::getFicheVersion($oFiche -> idFiche);
				$idFicheVersion = $lastVersion['idFicheVersion'];
			}
			
			$result = unlink($path . $oFiche -> idFiche . '-' . $idFicheVersion . '.xml');
			if ($result === false)
			{
				throw new Exception("Impossible de supprimer la version de la fiche : " . $oFiche -> idFiche);
			}
			
			$result = tsDatabase::query(self::SQL_DELETE_FICHE_VERSION, array($oFiche -> idFiche, $idFicheVersion));
			
			$lastVersion = self::getFicheVersion($oFiche -> idFiche);
			
			$result = file_put_contents($path . $oFiche -> idFiche . '.xml', $lastVersion['xmlTIF']);
			if ($result === false)
			{
				throw new Exception("Impossible de mettre à jour la version de la fiche : " . $oFiche -> idFiche);
			}
			
			self::updateSitFiche($oFiche -> idFiche);
		}
		
		
		
		
		private static function getFicheVersion($idFiche, $idFicheVersion = null)
		{
			$sqlFicheVersion = self::SQL_FICHE_VERSION . (is_null($idFicheVersion) ?  self::SQL_FICHE_VERSION_LASTVERSION : self::SQL_FICHE_VERSION_IDVERSION);
			$version = tsDatabase::getRow($sqlFicheVersion, array($idFiche, $idFicheVersion));
			
			if (is_null($version) === false)
			{
				$path = self::getPathByIdFiche($idFiche);
				$xmlFile = $path . $idFiche . '-' . (is_null($idFicheVersion) === false ? $idFicheVersion : $version['idFicheVersion']) . '.xml';
				
				if (file_exists($xmlFile) === false)
				{
					throw new ApplicationException("Impossible de charger le fichier xml de la version demandée : $idFiche");
				}
				
				$version['xmlTIF'] = file_get_contents($xmlFile);
			}
			
			return $version;
		}
		
		
		
		private static function getFicheVersionByEtat($idFiche, $etat = 'accepte')
		{
			$version = tsDatabase::getRow(self::SQL_FICHE_VERSION . self::SQL_FICHE_VERSION_ETAT . self::SQL_FICHE_VERSION_LASTVERSION, array($idFiche, $etat));
			
			return self::getFicheVersion($idFiche, $version['idFicheVersion']);
		}
		
		
		
		private static function updateSitFiche($idFiche)
		{
			$version = self::getFicheVersion($idFiche);
			$oFicheSimple = ficheSimpleModele::loadByXml($version['xmlTIF']);
			
			if ($oFicheSimple -> raisonSociale)
			{
				tsDatabase::query(self::SQL_UPDATE_RAISON_SOCIALE, array($oFicheSimple -> raisonSociale, $idFiche));
			}
			
			if ($oFicheSimple -> gpsLat && $oFicheSimple -> gpsLng)
			{
				tsDatabase::query(self::SQL_UPDATE_GPS, array($oFicheSimple -> gpsLat, $oFicheSimple -> gpsLng, $idFiche));
			}
		}
		
		
		
		public static function isXMLValide($xml)
		{
			// @todo
		}
		
		
		
		private static function getCodeTif($idFiche, $bordereau, $codeInsee)
		{
			return(strtoupper($bordereau) . tifTools::getCodeRegionByCodeInsee($codeInsee) .
						'0' . mb_substr($codeInsee, 0, 2, 'UTF-8') . tsConfig::get('TS_NUMERO_BASE') .
						str_repeat('0', 6 - strlen($idFiche)) . $idFiche);
		}
		
		
		
		private static function checkDroitFiche($idFiche)
		{
			if (tsDroits::isFicheAdministrable($idFiche) === false)
			{
				throw new ApplicationException("La fiche $idFiche n'est pas administrable");
			}
		}
		
		
		
		private function getPathByIdFiche($idFiche)
		{
			while (strlen($idFiche) < (tsConfig::get('TS_SUBFOLDERS_DEPTH_XML') * 2))
			{
				$idFiche = '0' . $idFiche;
			}
			
			$pathXml = tsConfig::get('TS_PATH_XML');
			$subFolders = str_split($idFiche, 2);
			foreach ($subFolders as $subFolder)
			{
				$pathXml .= $subFolder . '/';
				if (is_dir($pathXml) === false)
				{
					mkdir($pathXml);
				}
			}
			
			return $pathXml;
		}
		
		
	}
	
	
?>