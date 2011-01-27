<?php
	
/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
	
	require_once('application/collection/droitChampCollection.php');
	
	final class droitChampModele extends baseModele implements WSDLable
	{

		protected $idChamp;
		
		protected $visualisation;
		protected $modification;
		protected $validation;


		public function __construct()
		{
			$this -> loadDroit(0);
		}
		
		
		public function loadDroit($intDroit)
		{
			$this -> visualisation = (($intDroit & DROIT_VISUALISATION) > 0);
			$this -> modification = (($intDroit & DROIT_MODIFICATION) > 0);
			$this -> validation = (($intDroit & DROIT_VALIDATION) > 0);
		}
    
    
		public function getDroit()
		{
			$droit = $this -> visualisation * DROIT_VISUALISATION +
					$this -> modification * DROIT_MODIFICATION +
					$this -> validation * DROIT_VALIDATION;
			return $droit;
		}
    
    
		public function __toString()
		{
			$str = '<h3>Droit champ</h3>';
			$str .= '<h5>idDroit : ' . $this -> idDroit . '</h5>';
			$str .= '<h5>idChamp : ' . $this -> idChamp . '</h5>';
			$str .= '<h5>Controle champ : ' . $this -> controleChamp . '</h5>';
			$str .= '<h5>Validation : ' . $this -> validation . '</h5>';
			return $str;
		}
		
	}

?>