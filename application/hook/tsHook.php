<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	class tsHook
	{
	
	
		public static function hookObject($hookName, &$obj)
		{
			foreach (pluginDb::getPlugins() as $oPlugin)
			{
			//Logger::file($oPlugin);
				$pluginName = $oPlugin -> name;
				$functionName = $pluginName . '::hook' . ucfirst($hookName);
				if (function_exists($functionName))
				{
					$obj = $functionName($obj);
				}
			}
		}
		
		
		public static function hookArray($hookName, &$arr)
		{
			foreach (pluginDb::getPlugins() as $oPlugin)
			{
				$pluginName = $oPlugin -> name;
				$functionName = $pluginName . '::hook' . ucfirst($hookName);
				if (function_exists($functionName))
				{
					$arr = $functionName($arr);
				}
			}
		}
		
		
	}
	


?>