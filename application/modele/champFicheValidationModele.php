<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/collection/champFicheCollection.php');

	final class champFicheValidationModele extends baseModele implements WSDLable
	{
	
		protected $idChamp;
		protected $ancienneValeur;
		protected $nouvelleValeur;
		protected $etat;
		protected $libelle;
		
	}
	

?>