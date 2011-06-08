<?php
	
/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
	
	require_once('application/collection/utilisateurCollection.php');
	
	final class utilisateurModele extends baseModele implements WSDLable
	{
		
		protected $idUtilisateur;
		protected $email;
		protected $password;
		protected $typeUtilisateur;
		protected $idGroupe;


		public function __toString()
		{
			$str = '<h3>Utilisateur</h3>';
			$str .= '<h5>idUtilisateur : ' . $this -> idUtilisateur . '</h5>';
			$str .= '<h5>Email : ' . $this -> email . '</h5>';
			$str .= '<h5>Password : ' . $this -> password . '</h5>';
			$str .= '<h5>idGroupe : ' . $this -> idGroupe . '</h5>';
			$str .= '<h5>Type utilisateur : ' . $this -> typeUtilisateur . '</h5>';
			return $str;
		}
		
	}

?>