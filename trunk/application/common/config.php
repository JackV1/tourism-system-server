<?php

	tsConfig::set('BASE_PATH', '/path/to/your/server/');
	
	// Logger 
	tsConfig::set('TS_EMAIL_LOGS', 'nicolas@raccourci.fr');

	// Accès root
	tsConfig::set('TS_ROOT_SESSIONID', 'root');
	tsConfig::set('TS_ROOT_LOGIN', 'root');
	tsConfig::set('TS_ROOT_PASS', 'root');
	
	// Cache
	tsConfig::set('TS_CACHE', 'memcache');
	tsConfig::set('TS_CACHE_PREFIXE', 'keyMemcacheServer_');
	tsConfig::set('TS_MEMCACHE_SERVER', 'localhost');
	tsConfig::set('TS_MEMCACHE_PORT', '11211');

	// Database
	tsConfig::set('TS_BDD_TYPE', 'MySql');
	tsConfig::set('TS_BDD_SERVER', 'localhost');
	tsConfig::set('TS_BDD_USER', 'user');
	tsConfig::set('TS_BDD_PASSWORD', 'password');
	tsConfig::set('TS_BDD_NAME', 'base');
	
	// Thésaurus
	tsConfig::set('TS_THESAURUS_PREFIXE', '100');
	
	// Config générale
	tsConfig::set('TS_NUMERO_BASE', '1');
	
	// Chemins
	tsConfig::set('TS_PATH_EMPTYXML', BASE_PATH . 'application/tourinfrance/fichevierge.xml');
	
	tsConfig::set('TS_PATH_XML', BASE_PATH . 'xmlTIF/');
	tsConfig::set('TS_SUBFOLDERS_DEPTH_XML', 3);
	tsConfig::set('TS_PATH_ARCHIVES_XML', TS_PATH_XML . 'archives/');
	
	tsConfig::set('TS_PATH_MEDIAS', BASE_PATH . 'medias/');
	tsConfig::set('TS_URL_MEDIAS', 'http://yourmedia.com/');
	tsConfig::set('TS_SUBFOLDERS_DEPTH_MEDIAS', 2);
	
	// Droits généraux
	define('DROIT_GET', 1);
	define('DROIT_ADMIN', 2);
	define('DROIT_DELETE', 4);
	
	// Droits champ - fiche - territoire
	define('DROIT_VISUALISATION', 1);
	define('DROIT_MODIFICATION', 2);
	define('DROIT_VALIDATION', 4);
	define('DROIT_SUPPRESSION_FICHES', 8);
	define('DROIT_CREATION_FICHES', 16);
	define('DROIT_ADMINISTRATION', 32);
	
	// Plugins
	tsConfig::set('TS_URL_SYNCHRO', 'http://yourplugins.com/plugins.wsdl');

?>