<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	/**
	 * Classe parente des services
	 * Initialise l'application
	 * Gère les erreurs et les retours pour la conformité au wsdl
	 */
	abstract class wsEndpoint
	{
		
		
		/**
		 * Méthode d'appel, chaque méthode de service passe par ici
		 * Initialise l'application, charge les droits de l'utilisateur, puis appelle la méthode demandée
		 * Permet de centraliser la gestion des retours d'erreur (wsStatus) pour garder la conformité au wsdl
		 * @param string $method : méthode appelée
		 * @param array $arguments : tableau d'arguments passés
		 * @return : ce qui est retourné par la méthode appelée
		 */
		public function __call($method, $arguments)
		{
			try
			{
				// Load de l'application
				self::loadApplication();
				
				// Chargement des plugins
                //tsPlugins::loadPlugins();
                
				// Vérification et renouvellement de la session, load des droits
				tsDroits::restore($arguments[0]);
				tsDroits::load();
				
				// Dépile $arguments pour enlever le sessionId
				array_shift($arguments);
				
				$retour = array($method);
				
				if (method_exists($this, '_' . $method))
				{
					$retour = call_user_func_array(array($this, '_' . $method), $arguments);
					$retour = (is_array($retour) === false) ? array() : $retour;
				}
				else
				{
					throw new ApplicationException("La méthode demandée n'existe pas");
				}
				
				
				
				if (count(Logger::$errors) > 0)
				{
					$success = false;
					$errors = Logger::$errors;
					$errorLevel = 2;
					$errorCode = null;
				}
				elseif (count(Logger::$notices) > 0)
				{
					$success = false;
					$errors = Logger::$notices;
					$errorLevel = 1;
					$errorCode = null;
				}
				else
				{
					$success = true;
					$errors = null;
					$errorLevel = 0;
					$errorCode = null;
				}

			}
			catch(SecuriteException $e)
			{
				$success = false;
				$errors = $e -> getMessage();
				$errorLevel = 4;
				$errorCode = null;
				
				Logger::file($e -> getMessage());
			}
			catch(ApplicationException $e)
			{
				$success = false;
				$errors = $e -> getMessage();
				$errorLevel = 4;
				$errorCode = null;
				
				Logger::file($e -> getMessage());
			}
			catch(SessionException $e)
			{
				$success = false;
				$errors = $e -> getMessage();
				$errorLevel = 2;
				$errorCode = 510;
				
				Logger::file("Erreur de session");
			}
			catch(Exception $e)
			{
				$success = false;
				$errors = $e -> getMessage();
				$errorLevel = 3;
				$errorCode = null;
				
				Logger::file($e -> getMessage());
			}

			return array_merge(array('status' => new wsStatus($success, $errors, $errorLevel, $errorCode)), (array)$retour);
		}
		
		
		
		
		/**
		 * Initialisation de l'application (config, bdd, cache)
		 * @void
		 */
		final protected function loadApplication()
		{
			tsConfig::loadConfig('config');
			
			Logger::init(array(
				'email' => tsConfig::get('TS_EMAIL_LOGS'),
				'application' => 'Tourism System',
				'error_reporting' => 4,
				'user_reporting' => 'E_USER_ERROR,E_USER_WARNING',
				'verbose' => false,
				'encoding' => 'UTF-8'
			));
			
			
			tsDatabase::load(tsConfig::get('TS_BDD_TYPE'));
			tsDatabase::connect(
				tsConfig::get('TS_BDD_SERVER'),
				tsConfig::get('TS_BDD_USER'),
				tsConfig::get('TS_BDD_PASSWORD'));
			tsDatabase::selectDatabase(tsConfig::get('TS_BDD_NAME'));
			
			tsCache::load(tsConfig::get('TS_CACHE'));
		}
		
		
		
		
		/**
		 * Restriction des accès aux services
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function restrictAccess()
		{
			$authorizedUsers = func_get_args();
			if (in_array(tsDroits::getTypeUtilisateur(), $authorizedUsers) === false)
			{
				throw new SecuriteException("Droits insuffisants : ce service n'est pas disponible");
			}
		}
		
		
		/**
		 * 
		 * @param ficheModele $oFiche : 
		 * @param object   $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitFiche(ficheModele $oFiche, $droit)
		{
			//throw new SecuriteException("Droits insuffisants : ce service n'est pas disponible");
		}
		
		/**
		 * 
		 * @param groupeModele $oGroupe
		 * @param object    $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitGroupe(groupeModele $oGroupe, $droit)
		{
			
		}
		
		/**
		 * 
		 * @param champModele $oChamp
		 * @param object   $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitChamp(champModele $oChamp, $droit)
		{
			
		}
		
		/**
		 * 
		 * @param utilisateurModele $oUtilisateur
		 * @param object         $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitUtilisateur(utilisateurModele $oUtilisateur, $droit)
		{
			
		}
		
		/**
		 * 
		 * @param profilModele $oProfil
		 * @param object    $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitProfil(profilModele $oProfil, $droit)
		{
			
		}
		
		/**
		 * 
		 * @param territoireModele $oTerritoire
		 * @param object        $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitTerritoire(territoireModele $oTerritoire, $droit)
		{
			
		}
		
		/**
		 * 
		 * @param thesaurusModele $oThesaurus
		 * @param object       $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitThesaurus(thesaurusModele $oThesaurus, $droit)
		{
			
		}
		
		/**
		 * 
		 * @param bordereauModele $oBordereau
		 * @param communeModele   $oCommune
		 * @param object       $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitBordereauCommune(bordereauModele $oBordereau, communeModele $oCommune, $droit)
		{
			
		}
		
		/**
		 * 
		 * @param bordereauModele  $oBordereau
		 * @param territoireModele $oTerritoire
		 * @param object        $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitBordereauTerritoire(bordereauModele $oBordereau, territoireModele $oTerritoire, $droit)
		{
			
		}
		
		
		/**
		 * 
		 * @param communeModele $oCommune : 
		 * @param object $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitCommune(communeModele $oCommune, $droit)
		{
			
		}
		
	}


?>