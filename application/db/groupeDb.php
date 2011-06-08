<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/groupeModele.php');
	
	final class groupeDb
	{
	
		const SQL_GROUPE = "SELECT idGroupe, nomGroupe, idSuperAdmin FROM sitGroupe WHERE idGroupe='%d'";
		const SQL_GROUPES = "SELECT idGroupe FROM sitGroupe";
		const SQL_CREATE_GROUPE = "INSERT INTO sitGroupe (nomGroupe) VALUES('%s')";
		const SQL_UPDATE_GROUPE = "UPDATE sitGroupe SET nomGroupe='%s' WHERE idGroupe='%d'";
		const SQL_DELETE_GROUPE = "DELETE FROM sitGroupe WHERE idGroupe='%d'";
		const SQL_SET_SUPER_ADMIN_GROUPE = "UPDATE sitGroupe SET idSuperAdmin='%d' WHERE idGroupe='%d'";
		const SQL_UNSET_SUPER_ADMIN_GROUPE = "UPDATE sitGroupe SET idSuperAdmin=NULL WHERE idGroupe='%d'";
		const SQL_UTILISATEURS_GROUPE = "SELECT idUtilisateur, login, pass FROM sitUtilisateur WHERE idGroupe='%d'";
		const SQL_ADD_GROUPE_TERRITOIRE = "INSERT INTO sitGroupeTerritoire (idGroupe, idTerritoire) VALUES ('%d', '%d')";
		const SQL_DELETE_GROUPE_TERRITOIRE = "DELETE FROM sitGroupeTerritoire WHERE idGroupe='%d' AND idTerritoire='%d'";
		const SQL_GROUPE_TERRITOIRES = "SELECT idTerritoire FROM sitGroupeTerritoire WHERE idGroupe='%d'";
		
		
		public static function getGroupe($idGroupe)
		{
			if (is_numeric($idGroupe) === false)
			{
				throw new ApplicationException("L'identifiant de groupe n'est pas numérique");
			}
			$result = tsDatabase::getRow(self::SQL_GROUPE, array($idGroupe), DB_FAIL_ON_ERROR);
			$oGroupe = new groupeModele();
			$oGroupe -> setIdGroupe($result['idGroupe']);
			$oGroupe -> setIdSuperAdmin($result['idSuperAdmin']);
			$oGroupe -> setNomGroupe($result['nomGroupe']);
			return $oGroupe;// -> getObject();
		}
		
		
		public static function createGroupe($nomGroupe)
		{
			return tsDatabase::insert(self::SQL_CREATE_GROUPE, array($nomGroupe));
		}
		
		public static function updateGroupe($oGroupe, $nomGroupe)
		{
			return tsDatabase::query(self::SQL_UPDATE_GROUPE, array($nomGroupe, $oGroupe -> idGroupe));
		}
		
		public static function deleteGroupe(groupeModele $oGroupe)
		{
			return tsDatabase::query(self::SQL_DELETE_GROUPE, array($oGroupe -> idGroupe));
		}
		
		
		public static function setSuperAdminGroupe(groupeModele $oGroupe, utilisateurModele $oUtilisateur)
		{
			if($oUtilisateur -> idGroupe != $oGroupe -> idGroupe)
			{
				throw new SecuriteException("L'utilisateur fait partie d'un autre groupe, il ne peut pas être administrateur");
			}
			
			if ($oUtilisateur -> typeUtilisateur != 'admin')
			{
				throw new SecuriteException("Seuls les utilisateurs de type admin peuvent devenir administrateur du groupe");
			}
			
			return tsDatabase::query(self::SQL_SET_SUPER_ADMIN_GROUPE, array($oUtilisateur -> idUtilisateur, $oGroupe -> idGroupe));
		}
		
		public static function unsetSuperAdminGroupe(groupeModele $oGroupe)
		{
			return tsDatabase::query(self::SQL_UNSET_SUPER_ADMIN_GROUPE, array($oGroupe -> idGroupe));;
		}
		
		
		public static function getGroupes()
		{
			$oGroupeCollection = new GroupeCollection();
			$idGroupes = tsDatabase::getRecords(self::SQL_GROUPES, array());
			foreach($idGroupes as $idGroupe)
			{
				$oGroupeCollection[] = self::getGroupe($idGroupe);
			}
			return $oGroupeCollection -> getCollection();
		}
		
		
		public static function getGroupeTerritoires(groupeModele $oGroupe)
		{
			$oTerritoireCollection = new TerritoireCollection();
			$idTerritoires = tsDatabase::getRecords(self::SQL_GROUPE_TERRITOIRES, array($oGroupe -> idGroupe));
			foreach($idTerritoires as $idTerritoire)
			{
				$oTerritoireCollection[] = territoireDb::getTerritoire($idTerritoire);
			}
			return $oTerritoireCollection -> getCollection();
		}
		
		
		
		public static function getUtilisateursGroupe(groupeModele $oGroupe)
		{
			return tsDatabase::getRows(self::SQL_UTILISATEURS_GROUPE, array($oGroupe -> idGroupe));
		}

		
		
		public static function addGroupeTerritoire(groupeModele $oGroupe, territoireModele $oTerritoire)
		{
			return tsDatabase::insert(self::SQL_ADD_GROUPE_TERRITOIRE, array($oGroupe -> idGroupe, $oTerritoire -> idTerritoire));
		}
		
		
		public static function deleteGroupeTerritoire(groupeModele $oGroupe, territoireModele $oTerritoire)
		{
			return tsDatabase::query(self::SQL_DELETE_GROUPE_TERRITOIRE, array($oGroupe -> idGroupe, $oTerritoire -> idTerritoire));
		}
		
	}
	
	
?>