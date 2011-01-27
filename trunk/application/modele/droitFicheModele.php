<?php
	
/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
	
	require_once('application/collection/droitFicheCollection.php');
	
	final class droitFicheModele extends droitModele implements WSDLable
	{
		
		protected $idFiche;
		protected $raisonSociale;
		
		
		public function setAdministration($value)
		{
			return false;
		}
		
		public function setCreationFiches($value)
		{
			return false;
		}

	}

?>