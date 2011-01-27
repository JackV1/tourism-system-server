<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/utilisateurModele.php');
	require_once('application/modele/sessionModele.php');
	
	final class utilisateurDb
	{
	
		const SQL_UTILISATEUR = "SELECT idUtilisateur, login, pass, typeUtilisateur, idGroupe FROM sitUtilisateur WHERE idUtilisateur='%d'";
		const SQL_UTILISATEUR_EMAIL = "SELECT idUtilisateur FROM sitUtilisateur WHERE login='%s'";
		const SQL_CREATE_UTILISATEUR = "INSERT INTO sitUtilisateur (login, pass, typeUtilisateur, idGroupe) VALUES('%s', '%s', '%s', '%d')";
		const SQL_DELETE_UTILISATEUR = "DELETE FROM sitUtilisateur WHERE idUtilisateur='%d'";
		const SQL_IS_UTILISATEUR = "SELECT idUtilisateur FROM sitUtilisateur WHERE login='%s'";
		const SQL_GET_PASSWORD = "SELECT pass FROM sitUtilisateur WHERE idUtilisateur='%d'";
		const SQL_CHANGE_PASSWORD = "UPDATE sitUtilisateur SET pass='%s' WHERE idUtilisateur='%d'";
		const SQL_SESSIONS_UTILISATEUR = "SELECT sessionId, sessionStart, sessionEnd, ip FROM sitSessions WHERE idUtilisateur='%d'";
		
		
		public static function getUtilisateur($idUtilisateur)
		{
			if (is_numeric($idUtilisateur) === false)
			{
				throw new ApplicationException("L'identifiant d'utilisateur n'est pas numérique");
			}
			$result = tsDatabase::getRow(self::SQL_UTILISATEUR, array($idUtilisateur), DB_FAIL_ON_ERROR);
			$oUtilisateur = new utilisateurModele();
			$oUtilisateur -> setIdUtilisateur($result['idUtilisateur']);
			$oUtilisateur -> setIdGroupe($result['idGroupe']);
			$oUtilisateur -> setTypeUtilisateur($result['typeUtilisateur']);
			$oUtilisateur -> setEmail($result['login']);
			if (tsDroits::isRoot() === true)
			{
				$oUtilisateur -> setPassword($result['pass']);
			}
			return $oUtilisateur;// -> getObject();
		}
		
		
		public static function getUtilisateurByEmail($email)
		{
			$idUtilisateur = tsDatabase::getRecord(self::SQL_UTILISATEUR_EMAIL, array($email), DB_FAIL_ON_ERROR);
			return self::getUtilisateur($idUtilisateur);
		}
		
		
		
		public static function getUtilisateurs()
		{
			$oUtilisateurCollection = new UtilisateurCollection();
			$idUtilisateurs = tsDroits::getUtilisateursAdministrables();
			foreach($idUtilisateurs as $idUtilisateur)
			{
				$oUtilisateurCollection[] = self::getUtilisateur($idUtilisateur);
			}
			return $oUtilisateurCollection -> getCollection();
		}
		
		
		public static function getSessionsUtilisateur(utilisateurModele $oUtilisateur)
		{
			$oSessionCollection = new sessionCollection();
			$sessions = tsDatabase::getRows(self::SQL_SESSIONS_UTILISATEUR, array($oUtilisateur -> idUtilisateur));
			foreach($sessions as $session)
			{
				$oSession = new sessionModele();
				$oSession -> setSessionId($session['sessionId']);
				$oSession -> setIdUtilisateur($oUtilisateur -> idUtilisateur);
				$oSession -> setSessionStart($session['sessionStart']);
				$oSession -> setSessionEnd($session['sessionEnd']);
				$oSession -> setIp($session['ip']);
				$oSessionCollection[] = $oSession -> getObject();
			}
			return $oSessionCollection -> getCollection();
		}
		
		
		public static function createUtilisateur($email, $typeUtilisateur, $idGroupe)
		{

			if (self::isUtilisateur($email) === true)
			{
				throw new ApplicationException("L'utilisateur existe déjà");
			}
			
			if (self::isEmail($email) === false)
			{
				throw new ApplicationException("L'email n'est pas valide");
			}
			
			$pass = substr(md5(uniqid(mt_rand(), true)), 10, 6);
			return tsDatabase::insert(self::SQL_CREATE_UTILISATEUR, array($email, $pass, $typeUtilisateur, $idGroupe));
		}
		
		
		public static function deleteUtilisateur(utilisateurModele $oUtilisateur)
		{
			return tsDatabase::query(self::SQL_DELETE_UTILISATEUR, array($oUtilisateur -> idUtilisateur));
		}
		
		
		public static function changePassword($oldPassword, $newPassword, utilisateurModele $oUtilisateur)
		{
			if (self::isPasswordValide($newPassword) === false)
			{
				throw new ApplicationException("Le nouveau mot de passe n'est pas correct (4 à 64 caractères alphanumériques)");
			}
			$password = tsDatabase::getRecord(self::SQL_GET_PASSWORD, array($oUtilisateur -> idUtilisateur));
			if ($password != $oldPassword)
			{
				throw new ApplicationException("Le mot de passe n'est pas correct");
			}
			return tsDatabase::query(self::SQL_CHANGE_PASSWORD, array($newPassword, $oUtilisateur -> idUtilisateur));
		}
		
		
		public static function sendPassword(utilisateurModele $oUtilisateur)
		{
			$password = tsDatabase::getRecord(self::SQL_GET_PASSWORD, array($oUtilisateur -> idUtilisateur));
			mail($oUtilisateur -> email, 'Votre mot de passe Tourism System', $password);
			return true;
		}
		
		
		
		private static function isUtilisateur($email)
		{
			return (count(tsDatabase::getRecords(self::SQL_IS_UTILISATEUR, array($email))) >= 1);
		}
		
		
		private static function isEmail($email)
		{
			return (preg_match("/^[-+.\w]{1,64}@[-.\w]{1,64}\.[-.\w]{2,6}$/i", $email) == 1);
		}
		
		private static function isPasswordValide($password)
		{
			return (preg_match("/^[\w]{4,64}$/i", $password) == 1);
		}

		
	}
	
	
?>