<?php
	
/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
	
	require_once('application/collection/groupeCollection.php');	
	
	final class groupeModele extends baseModele implements WSDLable
	{
		
		protected $idSuperAdmin;
		protected $idGroupe;
		protected $nomGroupe;


		public function __toString()
		{
			$str = '<h3>Groupe</h3>';
			$str .= '<h5>idGroupe : ' . $this -> idGroupe . '</h5>';
			$str .= '<h5>Libellé : ' . $this -> nomGroupe . '</h5>';
			$str .= '<h5>Super admin : ' . $this -> idSuperAdmin . '</h5>';
			return $str;
		}
		
	}

?>