<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/champModele.php');
	require_once('application/modele/bordereauModele.php');
	
	final class champDb
	{
	
		const SQL_CHAMP = "SELECT identifiant, liste, libelle, xPath, bordereau FROM sitChamp WHERE idChamp='%d'";
		const SQL_CHAMP_IDENTIFIANT = "SELECT idChamp FROM sitChamp WHERE identifiant='%s'";
		const SQL_CHAMP_ENFANTS = "SELECT idChamp FROM sitChamp WHERE idChampParent='%d'";
		const SQL_CREATE_CHAMP = "INSERT INTO sitChamp (identifiant, libelle, xPath) VALUES ('%s', '%s', '%s')";
		const SQL_UPDATE_CHAMP = "UPDATE sitChamp SET identifiant='%s', libelle='%s', xPath='%s' WHERE idChamp='%d'";
		const SQL_UPDATE_CHAMP_NULL = "UPDATE sitChamp SET %s=NULL WHERE idChamp='%d'";
		const SQL_UPDATE_CHAMP_VALUE = "UPDATE sitChamp SET %s='%s' WHERE idChamp='%d'";
		const SQL_CHAMPS = "SELECT idChamp FROM sitChamp WHERE idChampParent IS NULL";
		const SQL_CHAMPS_BORDEREAU = " AND (FIND_IN_SET('%s', bordereau)>0 OR bordereau IS NULL)";
		const SQL_DELETE_CHAMP = "DELETE FROM sitChamp WHERE idChamp='%d'";
		
	
		public static function getChamp($idChamp)
		{
			if (is_numeric($idChamp) === false)
			{
				throw new ApplicationException("L'identifiant de champ n'est pas numérique");
			}
			$result = tsDatabase::getObject(self::SQL_CHAMP, array($idChamp), DB_FAIL_ON_ERROR);
			$oChamp = champModele::getInstance($result, 'champModele');
			
			// @todo : verifier que ca passe bien avec le SET
			//$oChamp -> setBordereau($result['bordereau']);
			$oChamp -> setIdChamp($idChamp);
			if (is_null($result -> idChampParent))
			{
				// Enfants pour champ tif complexe
				$oChampCollection = new ChampCollection();
				$idChamps = tsDatabase::getRecords(self::SQL_CHAMP_ENFANTS, array($idChamp));
				foreach($idChamps as $idChamp)
				{
					$oChampCollection[] = self::getChamp($idChamp);
				}
				$oChamp -> setChamps($oChampCollection -> getCollection());
				$oChamp = $oChamp -> getObject();
			}
			else
			{
				$oChamp -> setIdChampParent($result -> idChampParent);
				$oChamp -> setListe($result['liste']);
			}
			return $oChamp;
		}
		
		
		
		public static function getChampByIdentifiant($identifiant)
		{
			$idChamp = tsDatabase::getRecord(self::SQL_CHAMP_IDENTIFIANT, array($identifiant), DB_FAIL_ON_ERROR);
			return self::getChamp($idChamp);
		}
		
		
		
		public static function createChamp($identifiant, $libelle, $xPath, $liste, $bordereaux, $oChampParent)
		{
			$idChamp = tsDatabase::insert(self::SQL_CREATE_CHAMP, array($identifiant, $libelle, $xPath));
			if (is_null($liste) === false)
			{
				tsDatabase::query(self::SQL_UPDATE_CHAMP_VALUE, array('liste', $liste, $idChamp));
			}
			if (is_null($bordereaux) === false)
			{
				tsDatabase::query(self::SQL_UPDATE_CHAMP_VALUE, array('bordereau', $bordereaux, $idChamp));
			}
			if (is_null($oChampParent) === false)
			{
                tsDatabase::query(self::SQL_UPDATE_CHAMP_VALUE, array('idChampParent', $oChampParent -> idChamp, $idChamp));
			}
			return $idChamp;
		}
		
		
		
		public static function updateChamp(champModele $oChamp, $identifiant, $libelle, $xPath, $liste, $bordereaux)
		{
			tsDatabase::query(self::SQL_UPDATE_CHAMP, array($identifiant, $libelle, $xPath, $oChamp -> idChamp));
			
			if (is_null($liste))
			{
				tsDatabase::query(self::SQL_UPDATE_CHAMP_NULL, array('liste', $oChamp -> idChamp));
			}
			else
			{
				tsDatabase::query(self::SQL_UPDATE_CHAMP_VALUE, array('liste', $liste, $oChamp -> idChamp));
			}
			
			if (is_null($bordereaux))
			{
				tsDatabase::query(self::SQL_UPDATE_CHAMP_NULL, array('bordereau', $oChamp -> idChamp));
			}
			else
			{
				tsDatabase::query(self::SQL_UPDATE_CHAMP_VALUE, array('bordereau', $bordereaux, $oChamp -> idChamp));
			}
		}
		
		
		
		public static function getChamps($bordereau)
		{
			$oChampCollection = new ChampCollection();
			if (is_null($bordereau))
			{
				$idChamps = tsDatabase::getRecords(self::SQL_CHAMPS, array());
			}
			else
			{
				$oBordereau = new bordereauModele();
				$oBordereau -> setBordereau($bordereau);
				$idChamps = tsDatabase::getRecords(self::SQL_CHAMPS . self::SQL_CHAMPS_BORDEREAU, array($bordereau));
			}
			
			foreach($idChamps as $idChamp)
			{
				$oChampCollection[] = self::getChamp($idChamp);
			}
			return $oChampCollection -> getCollection();
		}
		
		
		
		public static function deleteChamp(champModele $oChamp)
		{
			return tsDatabase::query(self::SQL_DELETE_CHAMP, array($oChamp -> idChamp));
		}
		
		
	

	}
	
	
?>