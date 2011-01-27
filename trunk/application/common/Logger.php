<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */


	/**
	 * Classe permettant le debuggage et le log d'évènements
	 */
	class Logger
	{
		
		private static $instance;
		
		// Configuration par défaut du logger
		
		private static $mode = 'email';
		private static $email = 'nicolas@raccourci.fr';
		private static $application = 'Application';
		private static $user_reporting = 'E_USER_ERROR,E_USER_WARNING'; // E_USER_NOTICE
		private static $verbose = false;
		private static $error_reporting = 4;

		public static $errors = array();
		public static $notices = array();
		
		/**
		 * Ne logge plus les évènements
		 */
		public static function __sleep()
		{
			restore_error_handler();
		}
		
		/**
		 * Logge à nouveau les évènements
		 */
		public static function __wakeup()
		{
			self::init(array());
		}
		
		

		/**
		 * Gestion des erreurs utilisateur lancées par trigger_error
		 * Traitement spécifique selon leur niveau
		 * @return true
		 * @param $errno integer : niveau d'erreur
		 * @param $errstr string : message d'erreur
		 * @param $errfile : fichier sur lequel l'erreur a été identifiée
		 * @param $errline : ligne à laquelle l'erreur a été identifiée
		 */
		public static function userErrorHandler($errno, $errstr, $errfile, $errline)
		{
			switch ($errno)
			{
				// Erreur 
				case E_USER_ERROR:
					throw new ApplicationException("[$errno] MESSAGE : \"$errstr\" dans le fichier $errfile à la ligne $errline");
				break;
				// Avertissement
				case E_USER_WARNING:
					self::$errors[] = $errstr;
					//Logger::file("[$errno] $errstr dans le fichier $errfile à la ligne $errline");
		        break;
				// Notice
				case E_USER_NOTICE:
					self::$notices[] = $errstr;
		        break;
				// Autres erreurs
				default:
					if (self::$error_reporting >= $errno)
					{
						self::$notices[] = "Erreur ($errno) dans le fichier $errfile à la ligne $errline : <i>$errstr</i><br />";
					}
		        break;
		    }

		    return true;
		}
		
		
		
		
		/**
		 * Initialise le logger
		 * @param $params array : tableau associatif de paramêtres
		 * 			verbose -> mode verbeux
		 * 			mode -> ne sert pas
		 * 			email -> email d'envoi pour la réception des erreurs
		 * 			application -> nom de l'application
		 * 			user_reporting -> E_USER_ERROR,E_USER_WARNING,E_USER_NOTICE
		 * 			error_reporting -> niveau de rapport d'erreurs
		 */
		public static function init(array $params)
		{
			//ini_set('error_prepend_string', '<font style="font-family:Tahoma; font-size: 12px; color: #c00;">');
			//ini_set('error_append_string', '</font>');
			
			set_error_handler(array(__CLASS__, "userErrorHandler"));
			self::$verbose = (isset($params['verbose'])) ?
							$params['verbose'] : self::$verbose;
			self::$mode = (isset($params['mode'])) ?
							$params['mode'] : self::$mode;
			self::$email = (isset($params['email'])) ?
							$params['email'] : self::$email;
			self::$application = (isset($params['application'])) ?
							$params['application'] : self::$application;
			self::$user_reporting = (isset($params['user_reporting'])) ?
							$params['user_reporting'] : self::$user_reporting;
			self::$error_reporting = (isset($params['error_reporting'])) ?
							$params['error_reporting'] : self::$error_reporting;

			//error_reporting(self::$error_reporting);
		}
		
		/**
		 * Logge dans un fichier le message passé en paramêtre
		 * Les fichiers sont stockés sur l'arborescence dans le réportoire logs
		 * @param $message : N'importe quel type (objet, tableau, string)
		 */
		public static function file($message)
		{
			if (is_array($message) || is_object($message))
			{
				$message = var_export($message, true);
			}
			$periode = date('H');
			$filename = BASE_PATH . '/logs/' . date('Y-m-d') . '_log_'.$periode.'h.txt';
			$message = date('H:i:s') . ' -> ' . $message;
			$filecontent = (file_exists($filename)) ? file_get_contents($filename) : '';
			file_put_contents($filename, $filecontent . "\n" . $message);
			chmod($filename, 0777);
		}
		
		
				
		/**
		 * Envoie le message demandé par email
		 * Les fichiers sont stockés sur l'arborescence dans le réportoire logs
		 * @param $message : N'importe quel type (objet, tableau, string)
		 * @param $objet string [optional] : L'objet du message
		 */
		public static function email($message, $objet = null)
		{
			if (is_null($objet))
			{
				$objet = self::$application . ' Logger_' . date('Y-m-d H:i:s');
			}
			
			if (is_array($message) || is_object($message))
			{
				$message = var_export($message, true);
			}
			mail(self::$email, $objet, $message);
		}
		
		
		/**
		 * echo le message passé en paramêtre
		 * @param $message : N'importe quel type (objet, tableau, string)
		 */
		public static function debug($message)
		{
			if (is_array($message) || is_object($message))
			{
				echo '<pre>';
				var_dump($message);
				echo '</pre>';	
			}
			else
			{
				echo $message . '<br />';
			}
		}
		

		/**
		 * echo le message passé en paramêtre seulement en mode verbeux
		 * @param $message : N'importe quel type (objet, tableau, string)
		 */
		public static function log($message)
		{
			if (self::$verbose === true)
			{
				if (is_array($message) || is_object($message))
				{
					echo '<pre>';
					print_r($message);
					echo '</pre>';	
				}
				else
				{
					echo $message . '<br />';
				}
			}
		}
			
	}


?>