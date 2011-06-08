<?php
	
/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */


	header("Content-type: text/xml");
	
	require_once('application/utils/fonctions.php');
	require_once('application/utils/libXml.php');
	
	require_once('application/exception/ApplicationException.php');
	require_once('application/exception/CacheException.php');
	require_once('application/exception/DatabaseException.php');
	require_once('application/exception/SecuriteException.php');
	require_once('application/exception/ConfigException.php');
	
	require_once('application/config/tsConfig.php');
	require_once('application/common/tsSession.php');
	
	require_once('application/exception/ApplicationException.php');
	require_once('application/exception/CacheException.php');
	require_once('application/exception/ConfigException.php');
	require_once('application/exception/DatabaseException.php');
	require_once('application/exception/ImportException.php');
	require_once('application/exception/SessionException.php');
	
	require_once('application/database/tsDatabase.php');
	require_once('application/database/tsDatabaseInterface.php');
	
	require_once('application/cache/tsCache.php');
	require_once('application/cache/tsCacheInterface.php');
	
	require_once('application/droits/tsDroits.php');
	require_once('application/droits/tsDroitsInterface.php');
	
	require_once('services/endpoint/wsEndpoint.php');
	require_once('services/endpoint/wsStatus.php');
	
	require_once('application/modele/WSDLable.php');
	
	require_once('application/modele/baseModele.php');
	require_once('application/collection/baseCollection.php');
	
	require_once('application/common/Logger.php');

	require_once('services/endpoint/wsGroupe.php');
	require_once('services/endpoint/wsUtilisateur.php');
	require_once('services/endpoint/wsUtilisateurDroitTerritoire.php');
	require_once('services/endpoint/wsPlugin.php');
	
	require_once('application/hook/tsHook.php');
	
	ob_clean();
	
	
	
	
	$isPlugin = (file_exists('plugins/'.$_REQUEST['service'] . '/'));
	
	ini_set("soap.wsdl_cache_enabled", "0");

	try
	{
		$servicePath = '';
		$path = $isPlugin ? 'plugins/' . $_REQUEST['service'] . '/' : 'services/endpoint/';
		require_once($path . 'ws' . ucfirst($_REQUEST['service']) . '.php');
 		$t_soapServerOptions = array
		(
			'trace' => 1, 
			'soap_version' => SOAP_1_1
		);
		$arrPath = explode('/', $_SERVER['SCRIPT_NAME']);
		array_pop($arrPath);
		$basePath = implode('/', $arrPath);
		$basePath = defined('BASE_PATH') ? BASE_PATH : $basePath;
		$server = new SoapServer("http://".$_SERVER['SERVER_NAME'] . $basePath . ($isPlugin ? '/plugins/'.$_GET['service'].'/' :	'/services/wsdl/') . $_GET['service'] . '.wsdl', $t_soapServerOptions);
 		$server -> setclass('ws' . ucfirst($_GET['service']));
 		
	}
	catch (Exception $e)
	{
		Logger::file($e -> getMessage());
		echo $e -> getMessage();
		print_r($e);
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		ob_clean();
		$server -> handle();
	}
	
	
?>