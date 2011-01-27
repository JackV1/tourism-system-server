<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
 
	require_once('application/collection/ficheSimpleCollection.php');

	final class ficheSimpleModele extends baseModele implements WSDLable
	{
		
		protected $idFiche;
		protected $raisonSociale;
		protected $codeTIF;
		protected $codeInsee;
		protected $bordereau;
		protected $gpsLat;
		protected $gpsLng;
		protected $publication;
		protected $referenceExterne;
		
		
		public function __toString()
		{
			$str = '<h2>Fiche simple</h2>';
			$str .= '<h4>idFiche : ' . $this -> idFiche . '</h4>';
			$str .= '<h4>Code TIF : ' . $this -> codeTIF . '</h4>';
			$str .= '<h4>Raison sociale : ' . $this -> raisonSociale . '</h4>';
			$str .= '<h4>Bordereau : ' . $this -> bordereau . '</h4>';
			$str .= '<h4>Code insee : ' . $this -> codeInsee . '</h4>';
			$str .= '<h4>Latitude : ' . $this -> gpsLat . '</h4>';
			$str .= '<h4>Longitude : ' . $this -> gpsLng . '</h4>';
			return $str;
		}
		
	}


?>