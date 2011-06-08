<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class tsCache
	{
		
		
		private static $me;
		private static $instance;
		private static $cacheType;
		
		
		/**
		 * Constructeur de la classe tsCache
		 * @param string $cacheType : type de cache à utiliser
		 */
		private function __construct($cacheType)
		{
			if (defined('CACHE_LOADED'))
			{
				throw new ApplicationException("Le cache est déjà initialisé");
			}
			
			define('CACHE_LOADED', true);
			self::$cacheType = $cacheType;
			$this -> factory();
		}
		
		
		
		/**
		 * Singleton
		 * @param string $databaseType : type de connection à utiliser
		 * @return object : instance de la classe tsDatabase  
		 */
		public static function load($cacheType)
		{
			if (isset(self::$me) === false)
			{
				$c = __CLASS__;
				self::$me = new $c(strtolower($cacheType));
			}
		}
		
		
		
		/**
		 * Factory de la classe tsCache[type]
		 */
		private function factory()
		{
			switch (self::$cacheType)
			{
				case 'memcache':
					require_once('application/cache/tsCacheMemcache.php');
					self::$instance = new tsCacheMemcache();
				break;
				case 'session':
					require_once('application/cache/tsCacheSession.php');
					self::$instance = new tsCacheSession();
				break;
				default:
					throw new ApplicationException("Le cache n'a pas pu être initialisé");
				break;
			}
		}
		
		
		
		
		
		
		
		public static function set($varName, $value, $timeOut = null)
		{
			return self::$instance -> set(tsConfig::get('TS_CACHE_PREFIXE') . $varName, $value, $timeOut);
		}
		
		
		public static function get($varName)
		{	
			return self::$instance -> get(tsConfig::get('TS_CACHE_PREFIXE') . $varName);
		}
		
		
		public static function delete($varName)
		{
			self::$instance -> delete(tsConfig::get('TS_CACHE_PREFIXE') . $varName);
		}
		

	}


?>