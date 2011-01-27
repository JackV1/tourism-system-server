<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class tsPlugins
	{
		
		
		private static $me;
		private static $instance;
		
		
		/**
		 * Constructeur de la classe tsPlugins
		 * @param string $cacheType : type de cache à utiliser
		 */
		private function __construct()
		{
			
		}
		
		
		
		/**
		 * Singleton
		 * @return object : instance de la classe tsDatabase
		 */
		public static function loadPlugins() 
	    {
	        if (isset(self::$me) === false)
			{
	            $c = __CLASS__;
	            self::$me = new $c(strtolower($cacheType));
	        }
	    }
		
		
		
		
		

	}


?>