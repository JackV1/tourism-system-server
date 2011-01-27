<?php

	// Logger 
	tsConfig::set('TS_EMAIL_LOGS', 'nicolas@raccourci.fr');

	// Accès root
	tsConfig::set('TS_ROOT_SESSIONID', 'rootsession');
	tsConfig::set('TS_ROOT_LOGIN', 'root');
	tsConfig::set('TS_ROOT_PASS', 'password');
	
	// Cache
	tsConfig::set('TS_CACHE', 'memcache');
	tsConfig::set('TS_MEMCACHE_SERVER', 'localhost');
	tsConfig::set('TS_MEMCACHE_PORT', '11211');

	// Database
	tsConfig::set('TS_BDD_TYPE', 'MySql');
	tsConfig::set('TS_BDD_SERVER', 'localhost');
	tsConfig::set('TS_BDD_USER', 'tourismsystem');
	tsConfig::set('TS_BDD_PASSWORD', 'password');
	tsConfig::set('TS_BDD_NAME', 'tourismsystem');
	
	// Thésaurus
	tsConfig::set('TS_THESAURUS_PREFIXE', '100');
	
	// Config générale
	tsConfig::set('TS_NUMERO_BASE', '1');
	
	// Chemins
	tsConfig::set('TS_PATH_EMPTYXML', 'application/tourinfrance/fichevierge.xml');
	tsConfig::set('TS_PATH_MEDIAS', '/path/to/medias');
	
	tsConfig::set('TS_URL_MEDIAS', 'http://medias/');
	
	
	
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
	
	
	// Chemins
	tsConfig::set('TS_URL_SYNCHRO', 'http://plugins.tourism-system.fr/plugins.wsdl');

?>