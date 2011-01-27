<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/collection/bordereauCollection.php');
	
	final class bordereauModele extends baseModele implements WSDLable
	{
		
		protected $bordereau;
		protected $libelle;
		
		// @todo : hook bordereaux
		private $bordereaux = array('HOT', 'HPA', 'HLO', 'FMA', 'DEG', 'RES', 'ITI', 'PRD', 'ASC', 'LOI', 'PNA', 'PCU', 'VIL', 'ORG');
		
		
		public function setBordereau($bordereau)
		{
			if (in_array(strtoupper($bordereau), $this -> bordereaux))
			{
				$this -> bordereau = strtoupper($bordereau);
			}
		}
		
		public function __toString()
		{
			$str = '<h3>Bordereau</h3>';
			$str .= '<h5>Libellé : ' . $this -> libelle . '</h5>';
			$str .= '<h5>Code bordereau : ' . $this -> bordereau . '</h5>';
			return $str;
		}
	
	
	}

?>