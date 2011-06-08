<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/droitModele.php');
	require_once('application/modele/droitTerritoireModele.php');
	require_once('application/modele/bordereauModele.php');
	require_once('application/modele/droitChampModele.php');
	
	final class utilisateurDroitTerritoireDb
	{
		// @todo : UNION à ajouter pour le profil ??
		// Jermey
		//const SQL_DROIT = "SELECT droit FROM sitUtilisateurDroitTerritoire WHERE idUtilisateur='%d' AND bordereau='%s' AND idTerritoire='%d' AND idProfil IS NULL";
		const SQL_DROIT = "SELECT droit FROM sitUtilisateurDroitTerritoire WHERE idUtilisateur='%d' AND bordereau='%s' AND idTerritoire='%d'";
		//const SQL_DROIT = "SELECT droit FROM sitUtilisateurDroitTerritoire WHERE idUtilisateur='%1\$d' AND bordereau='%2\$s' AND idTerritoire='%3\$d' AND droit IS NOT NULL UNION SELECT p.droit FROM sitProfilDroit p, sitUtilisateurDroitTerritoire udt WHERE idUtilisateur='%1\$d' AND bordereau='%2\$s' AND idTerritoire='%3\$d' AND p.idProfil=udt.idProfil AND p.droit IS NOT NULL";
		const SQL_DROITS_UTILISATEUR = "SELECT bordereau, idTerritoire FROM sitUtilisateurDroitTerritoire WHERE idUtilisateur='%d'";
		const SQL_DROIT_CHAMP = "SELECT droit, idChamp FROM sitUtilisateurDroitTerritoireChamp WHERE idUtilisateur='%d' AND bordereau='%s' AND idTerritoire='%d'";
		const SQL_SET_DROIT = "REPLACE INTO sitUtilisateurDroitTerritoire (idUtilisateur, bordereau, idTerritoire, idProfil, droit) VALUES ('%d', '%s', '%d', NULL, '%d')";
		const SQL_SET_DROIT_CHAMP = "REPLACE INTO sitUtilisateurDroitTerritoireChamp (idUtilisateur, bordereau, idTerritoire, idChamp, droit) VALUES ('%d', '%s', '%d', '%d', '%d')";
		const SQL_DELETE_DROIT = "DELETE FROM sitUtilisateurDroitTerritoire WHERE idUtilisateur='%d' AND bordereau='%s' AND idTerritoire='%d'";
		const SQL_DELETE_DROIT_CHAMP = "DELETE FROM sitUtilisateurDroitTerritoireChamp WHERE idUtilisateur='%d' AND bordereau='%s' AND idTerritoire='%d' AND idChamp='%d'";
		const SQL_DELETE_DROITS_CHAMP = "DELETE FROM sitUtilisateurDroitTerritoireChamp WHERE idUtilisateur='%d' AND bordereau='%s' AND idTerritoire='%d'";
		//@modification sur la requête suivante by Robert F.
		//const SQL_SET_DROIT_PROFIL = "INSERT INTO sitUtilisateurDroitTerritoire (idUtilisateur, bordereau, idTerritoire, idChamp, idProfil, droit) VALUES ('%d', '%s', '%d', NULL, '%d', NULL)";
		const SQL_SET_DROIT_PROFIL = "INSERT INTO sitUtilisateurDroitTerritoire (idUtilisateur, bordereau, idTerritoire, idProfil, droit) VALUES ('%d', '%s', '%d', '%d', NULL)";
		
		
		
		
		public static function getDroitTerritoire(utilisateurModele $oUtilisateur, bordererauModele $oBordereau, territoireModele $oTerritoire)
		{
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$bordereau = $oBordereau -> bordereau;
			$idTerritoire = $oTerritoire -> idTerritoire;
			$result = tsDatabase::getRow(self::SQL_DROIT, array($idUtilisateur, $bordereau, $idTerritoire), DB_FAIL_ON_ERROR);
			$oDroit = new droitTerritoireModele();
			$oDroit -> setBordereau($bordereau);
			$oDroit -> setLibelleTerritoire($oTerritoire -> libelle);
			$oDroit -> setIdTerritoire($idTerritoire);
			$oDroit -> loadDroit($result['droit']);
			
			$oDroit -> setDroitsChamp(self::getDroitsTerritoireChamp($oUtilisateur, $oBordereau, $oTerritoire));
			
			return $oDroit;
		}
		
		
		private static function getDroitsTerritoireChamp(utilisateurModele $oUtilisateur, bordererauModele $oBordereau, territoireModele $oTerritoire)
		{
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$bordereau = $oBordereau -> bordereau;
			$idTerritoire = $oTerritoire -> idTerritoire;
			
			$oDroitChampCollection = new droitChampCollection();
			$droitsChamp = tsDatabase::getRows(self::SQL_DROIT_CHAMP, array($idUtilisateur, $bordereau, $idTerritoire));
			foreach($droitsChamp as $droitChamp)
			{
				$oDroit = new droitChampModele();
				$oDroit -> setIdChamp($droitChamp['idChamp']);
				$oDroit -> loadDroit($droitChamp['droit']);
				$oDroitChampCollection[] = $oDroit -> getObject();
			}
			return $oDroitChampCollection -> getCollection();
		}
		
		
		public static function getDroitsTerritoire(utilisateurModele $oUtilisateur)
		{
			$oDroitTerritoireCollection = new droitTerritoireCollection();
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$droitsTerritoire = tsDatabase::getRows(self::SQL_DROITS_UTILISATEUR, array($idUtilisateur));
			foreach($droitsTerritoire as $droitTerritoire)
			{
				$oBordereau = new bordereauModele();
				$oBordereau -> setBordereau($droitTerritoire['bordereau']);
				$oTerritoire = territoireDb::getTerritoire($droitTerritoire['idTerritoire']);
				$oDroit = self::getDroitTerritoire($oUtilisateur, $oBordereau, $oTerritoire);
				$oDroitTerritoireCollection[] = $oDroit -> getObject();
			}
			return $oDroitTerritoireCollection -> getCollection();
		}
		
		
		/*public static function getDroitTerritoireChamp(utilisateurModele $oUtilisateur, bordereauModele $oBordereau, territoireModele $oTerritoire, champModele $oChamp)
		{
			$idUtilisateur = $oUtilisateur -> getIdUtilisateur();
			$bordereau = $oBordereau -> getBordereau();
			$idTerritoire = $oTerritoire -> idTerritoire;
			$idChamp = $oChamp -> getIdChamp();
			
			$result = tsDatabase::getRow(self::SQL_DROIT_CHAMP, array($idUtilisateur, $bordereau, $idTerritoire, $idChamp), DB_FAIL_ON_ERROR);
			$oDroit = new droitChampModele();
			$oDroit -> loadDroit($result['droit']);
			return $oDroit;
		}*/
		
		
		public static function setDroitTerritoire(utilisateurModele $oUtilisateur, bordereauModele $oBordereau, territoireModele $oTerritoire, droitTerritoireModele $oDroit)
		{
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$bordereau = $oBordereau -> bordereau;
			$idTerritoire = $oTerritoire -> idTerritoire;
			$droit = $oDroit -> getDroit();
			
			return tsDatabase::query(self::SQL_SET_DROIT, array($idUtilisateur, $bordereau, $idTerritoire, $droit), DB_FAIL_ON_ERROR);
		}
		
		
		public static function setDroitTerritoireChamp(utilisateurModele $oUtilisateur, bordereauModele $oBordereau, territoireModele $oTerritoire, champModele $oChamp, droitChampModele $oDroit)
		{
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$bordereau = $oBordereau -> bordereau;
			$idTerritoire = $oTerritoire -> idTerritoire;
			$idChamp = $oChamp -> idChamp;
			$droit = $oDroit -> getDroit();
			
			return tsDatabase::query(self::SQL_SET_DROIT_CHAMP, array($idUtilisateur, $bordereau, $idTerritoire, $idChamp, $droit), DB_FAIL_ON_ERROR);
		}
		
		
		public static function deleteDroitTerritoire(utilisateurModele $oUtilisateur, bordereauModele $oBordereau, territoireModele $oTerritoire)
		{
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$bordereau = $oBordereau -> bordereau;
			$idTerritoire = $oTerritoire -> idTerritoire;
			$deleteChamps = tsDatabase::query(self::SQL_DELETE_DROITS_CHAMP, array($idUtilisateur, $bordereau, $idTerritoire));
			$deleteDroit = tsDatabase::query(self::SQL_DELETE_DROIT, array($idUtilisateur, $bordereau, $idTerritoire));
			return $deleteChamps && $deleteDroit;
		}
		
		
		public static function deleteDroitTerritoireChamp(utilisateurModele $oUtilisateur, bordereauModele $oBordereau, territoireModele $oTerritoire, champModele $oChamp)
		{
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$bordereau = $oBordereau -> bordereau;
			$idTerritoire = $oTerritoire -> idTerritoire;
			$idChamp = $oChamp -> idChamp;
			
			return tsDatabase::query(self::SQL_DELETE_DROIT_CHAMP, array($idUtilisateur, $bordereau, $idTerritoire, $idChamp));
		}
		
		
		public static function setDroitTerritoireProfil(utilisateurModele $oUtilisateur, bordereauModele $oBordereau, territoireModele $oTerritoire, profilModele $oProfil)
		{
			// Suppression de l'ancien droit sur fiche
			self::deleteDroitTerritoire($oUtilisateur, $oBordereau, $oTerritoire);
			
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$bordereau = $oBordereau -> bordereau;
			$idTerritoire = $oTerritoire -> idTerritoire;
			$idProfil = $oProfil -> idProfil;
			
			return tsDatabase::query(self::SQL_SET_DROIT_PROFIL, array($idUtilisateur, $bordereau, $idTerritoire, $idProfil));
		}
		
		
		
	}
	
	
?>