<?php

/**
 * @version		0.3 alpha-test - 2013-01-25
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/collection/champCollection.php');

	final class champModele extends baseModele implements WSDLable
	{
	
		protected $idChamp;
		protected $identifiant;
		protected $libelle;
		protected $xPath;
		protected $liste;
		protected $bordereau;
		protected $cle;
		
		protected $champs = array();

	}
	

?>