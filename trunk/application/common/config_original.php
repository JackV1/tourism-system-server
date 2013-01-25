<?php

	tsConfig::set('BASE_URL', 'http://www.url-to-your-serveur.fr/');
	tsConfig::set('BASE_PATH', '/path/to/your/server/');

	// Logger
	tsConfig::set('TS_EMAIL_LOGS', 'admin@domain.fr');

	// Cache
	tsConfig::set('TS_CACHE' , 'memcache');
	tsConfig::set('TS_CACHE_PREFIXE', 'cacheServeur_');
	tsConfig::set('TS_MEMCACHE_SERVER', 'localhost');
	tsConfig::set('TS_MEMCACHE_PORT', '11211');

	// Database
	tsConfig::set('TS_BDD_TYPE', 'MySql');
	tsConfig::set('TS_BDD_SERVER', 'localhost');
	tsConfig::set('TS_BDD_USER', '');
	tsConfig::set('TS_BDD_PASSWORD', '');
	tsConfig::set('TS_BDD_NAME', '');

	// Thésaurus
	tsConfig::set('TS_THESAURUS_PREFIXE', '99');

	// Config générale
	tsConfig::set('TS_NUMERO_BASE', '0');

	// Chemins
	tsConfig::set('TS_PATH_EMPTYXML', BASE_PATH . 'application/tourinfrance/fichevierge.xml');

	tsConfig::set('TS_PATH_MEDIAS', '/path/to/your/medias/');
	tsConfig::set('TS_URL_MEDIAS', 'http://www.url-to-your-medias.fr/');
	tsConfig::set('TS_SUBFOLDERS_DEPTH_MEDIAS', 2);

	tsConfig::set('TS_PATH_XML', '/path/to/your/xml/');
	tsConfig::set('TS_SUBFOLDERS_DEPTH_XML', 3);
	tsConfig::set('TS_PATH_ARCHIVES_XML', TS_PATH_XML . 'archives/');

	tsConfig::set('TS_PATH_PDF', BASE_PATH . 'ressources/fichePdf/');
	tsConfig::set('TS_URL_PDF', BASE_URL . 'ressources/fichePdf/');

	tsConfig::set('TS_PATH_LOGS', '/path/to/your/logs/');

	// Accès root
	tsConfig::set('TS_ROOT_SESSIONID', 'sessionidroot');
	tsConfig::set('TS_ROOT_LOGIN', 'root');
	tsConfig::set('TS_ROOT_PASS', 'password');

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
	tsConfig::set('TS_PATH_PLUGINS', BASE_PATH . 'plugins/');
	tsConfig::set('TS_URL_SYNCHRO', 'http://plugins.tourism-system.fr/plugins.wsdl');

?>
