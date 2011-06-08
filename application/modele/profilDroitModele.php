<?php
	
/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
 
	require_once('application/collection/profilDroitCollection.php');
	
	final class profilDroitModele extends droitModele implements WSDLable
	{
		
		protected $idGroupe;
		protected $libelle;
		protected $idProfil;
		
		protected $droitsChamp = array();

	}

?>