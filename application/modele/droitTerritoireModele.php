<?php
	
/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
	
	require_once('application/collection/droitTerritoireCollection.php');
	
	final class droitTerritoireModele extends droitModele implements WSDLable
	{
		
		protected $bordereau;
		protected $idTerritoire;
		protected $libelleTerritoire;
		protected $droitsChamp = array();

		public function setDroitsChamp(object $droitsChamp)
		{
			$this -> droitsChamp = $droitsChamp;
		}

	}

?>