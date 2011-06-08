<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
 
	final class tsDroits
	{
		
		private static $controller;
		private static $instance;
		private static $userType;
		private static $idUtilisateur;
		private static $idSession;
		
		
		const SQL_USERTYPE = "SELECT typeUtilisateur FROM sitUtilisateur WHERE idUtilisateur='%d'";
		const SQL_IS_SUPERADMIN = "SELECT idSuperAdmin FROM sitGroupe WHERE idSuperAdmin='%d'";
		const SQL_CONNECTION = "SELECT idUtilisateur FROM sitUtilisateur WHERE login='%s' AND pass='%s' AND idGroupe IS NOT NULL";
		
		private function __construct()
		{
			if (defined('DROITS_LOADED'))
			{
				throw new ApplicationException("Les droits sont déjà chargés");
			}
			
			define('DROITS_LOADED', true);
			$this -> getUserType();
			$this -> factory();
		}
		
		

		public static function __call($method, $arguments)
		{
			if (method_exists(self::$controller, $method))
			{
				return(call_user_func_array(array(self::$controller, $method), $arguments));
			}
			else
			{
				throw new ApplicationException("La méthode demandée dans " . __CLASS__ . " n'existe pas");
			}
		}
		
		

		public static function load()
 		{
 			if (isset(self::$instance) === false)
			{
				$c = __CLASS__;
				self::$instance = new $c();
 			}
 		}
		
		
		public static function connect($login, $pass)
		{
			$id = tsDatabase::getRecord(self::SQL_CONNECTION, array($login, $pass));
			if ($id === false)
			{
				// Connection root ?
				if ($login == tsConfig::get('TS_ROOT_LOGIN')
					&& $pass == tsConfig::get('TS_ROOT_PASS'))
				{
					self::$idUtilisateur = 0;
					return tsConfig::get('TS_ROOT_SESSIONID');
				}
				else
				{
					throw new ApplicationException("Les identifiants fournis ne sont pas corrects");
					return false;
				}
			}
			self::$idUtilisateur = $id;
			return tsSession::initSession($id);
		}
		
		
		public static function restore($idSession)
		{
			self::$idUtilisateur = ($idSession == tsConfig::get('TS_ROOT_SESSIONID')) ?
								0 : tsSession::getIdUtilisateur($idSession);
			self::$idSession = $idSession;
		}
		
		
		private function getUserType()
		{
			if (self::$idUtilisateur == 0)
			{
				self::$userType = 'root';
			}
			else
			{
				self::$userType = (tsDatabase::getRecord(self::SQL_IS_SUPERADMIN, array(self::$idUtilisateur)) === false) ?
									tsDatabase::getRecord(self::SQL_USERTYPE, array(self::$idUtilisateur)) : 'superadmin';
			}
		}
		
		
		
		private function factory()
		{
			require_once('application/droits/tsDroitsDefault.php');
			switch(strtolower(self::$userType))
			{
				case 'root':
					require_once('application/droits/tsDroitsRoot.php');
					self::$controller = new tsDroitsRoot(self::$idUtilisateur);
				break;
				case 'superadmin':
					require_once('application/droits/tsDroitsAdmin.php');
					require_once('application/droits/tsDroitsSuperAdmin.php');
					self::$controller = new tsDroitsSuperAdmin(self::$idUtilisateur);
				break;
				case 'admin':
					require_once('application/droits/tsDroitsAdmin.php');
					self::$controller = new tsDroitsAdmin(self::$idUtilisateur);
				break;
				case 'desk':
					require_once('application/droits/tsDroitsDesk.php');
					self::$controller = new tsDroitsDesk(self::$idUtilisateur);
				break;
				case 'manager':
					require_once('application/droits/tsDroitsManager.php');
					self::$controller = new tsDroitsManager(self::$idUtilisateur);
				break;
				default:
					throw new ApplicationException("Le type d'utilisateur n'est pas correct");
				break;
			}
			self::$controller -> loadDroits();
		}
		
		
		
		
		
		public static function getIdUtilisateur()
		{
			return self::$idUtilisateur;
		}

		public static function getIdSession()
		{
			return self::$idSession;
		}

		public static function getTypeUtilisateur()
		{
			return self::$userType;
		}
		
		public static function isRoot()
		{
			return(self::$userType == 'root');
		}
		
		public static function getGroupeUtilisateur()
		{
			return self::$controller -> getGroupeUtilisateur();
		}
		
		public static function getUtilisateursAdministrables()
		{
			return self::$controller -> getUtilisateursAdministrables();
		}
		
		public static function getFichesAdministrables()
		{
			return self::$controller -> getFichesAdministrables();
		}
		
		public static function getBordereauxAdministrables()
		{
			return self::$controller -> getBordereauxAdministrables();
		}
		
		
		
		public static function getDroitGroupe(groupeModele $oGroupe)
		{
			return self::$controller -> getDroitGroupe($oGroupe);
		}
		
		public static function getDroitChamp(champModele $oChamp)
		{
			return self::$controller -> getDroitChamp($oChamp);
		}
		
		public static function getDroitFicheChamp(ficheModele $oFiche, champModele $oChamp, $droitFiche)
		{
			$result = self::$controller -> getDroitFicheChamp($oFiche, $oChamp);
			if ($result !== false)
			{
				return $result;
			}
			else
			{
				$droitChamp = new droitChampModele();
				$droitChamp -> loadDroit($droitFiche);
				return $droitChamp -> getDroit();
			}
		}
		
		public static function getDroitFiche(ficheModele $oFiche)
		{
			return self::$controller -> getDroitFiche($oFiche);
		}
		
		public static function getDroitUtilisateur(utilisateurModele $oUtilisateur)
		{
			return self::$controller -> getDroitUtilisateur($oUtilisateur);
		}
		
		public static function getDroitProfil(profilModele $oProfil)
		{
			return self::$controller -> getDroitProfil($oProfil);
		}
		
		public static function getDroitTerritoire(territoireModele $oTerritoire)
		{
			return self::$controller -> getDroitTerritoire($oTerritoire);
		}
		
		public static function getDroitThesaurus(thesaurusModele $oThesaurus)
		{
			return self::$controller -> getDroitThesaurus($oThesaurus);
		}
		
		public static function getDroitBordereauTerritoire(bordereauModele $oBordereau, territoireModele $oTerritoire)
		{
			return self::$controller -> getDroitBordereauTerritoire($oBordereau, $oTerritoire);
		}
		
		public static function getDroitBordereauCommune(bordereauModele $oBordereau, communeModele $oCommune)
		{
			return self::$controller -> getDroitBordereauCommune($oBordereau, $oCommune);
		}
		

	}
	

?>