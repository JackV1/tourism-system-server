<?php

/**
 * @version		0.3 alpha-test - 2013-01-25
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/collection/champFicheValidationCollection.php');

	final class champFicheValidationModele extends baseModele implements WSDLable
	{
	
		protected $idValidationChamp;
		protected $idFiche;
		protected $idChamp;
		protected $identifiant;
		protected $libelle;
		protected $valeur;
		protected $idUtilisateur;
		protected $dateModification;
		protected $etat;
		protected $idValidateur;
		protected $dateValidation;
		
	}
	

?>