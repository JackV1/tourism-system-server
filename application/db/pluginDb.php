<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/pluginModele.php');
	
	final class pluginDb
	{
	
		const SQL_PLUGINS = "SELECT nomPlugin FROM sitPlugin";
		const SQL_PLUGIN = "SELECT nomPlugin, version, actif, dateMaj FROM sitPlugin WHERE nomPlugin='%s'";
		const SQL_DELETE_PLUGIN = "DELETE FROM sitPlugin WHERE nomPlugin='%s' AND actif='N'";
		const SQL_CREATE_PLUGIN = "INSERT INTO sitPlugin (nomPlugin, version, actif, dateMaj) VALUES ('%s', '%s', 'N', NOW())";
		const SQL_UPDATE_PLUGIN = "UPDATE sitPlugin SET version='%s' WHERE nomPlugin='%s'";
		const SQL_ENABLE_PLUGIN = "UPDATE sitPlugin SET actif='Y' WHERE nomPlugin='%s'";
		const SQL_DISABLE_PLUGIN = "UPDATE sitPlugin SET actif='N' WHERE nomPlugin='%s'";

		
		public static function getPlugin($nomPlugin)
		{
			$result = tsDatabase::getRow(self::SQL_PLUGIN, array($nomPlugin), DB_FAIL_ON_ERROR);
			$oPlugin = new pluginModele();
			$oPlugin -> setNomPlugin($result['nomPlugin']);
			$oPlugin -> setVersion($result['version']);
			//$oPlugin -> setCle($result['cle']);
			$oPlugin -> setActif($result['actif']);
			$oPlugin -> setDateMaj($result['dateMaj']);
			return $oPlugin;
		}
		
		
		
		public static function getPlugins()
		{
			$oPluginCollection = new PluginCollection();
			$nomPlugins = tsDatabase::getRecords(self::SQL_PLUGINS, array());
			foreach($nomPlugins as $nomPlugin)
			{
				$oPluginCollection[] = self::getPlugin($nomPlugin) -> getObject();
			}
			return $oPluginCollection -> getCollection();
		}

		
		public static function disablePlugin($nomPlugin)
		{
			return tsDatabase::query(self::SQL_DISABLE_PLUGIN, array($nomPlugin));
		}
		
		
		public static function enablePlugin($nomPlugin)
		{
			return tsDatabase::query(self::SQL_ENABLE_PLUGIN, array($nomPlugin));
		}
		
		
		public static function installPlugin($nomPlugin, $version)
		{
			return tsDatabase::insert(self::SQL_CREATE_PLUGIN, array($nomPlugin, $version));
		}
		

		public static function unInstallPlugin($nomPlugin)
		{
			return tsDatabase::query(self::SQL_DELETE_PLUGIN, array($nomPlugin));
		}
		
	}
	
	
?>