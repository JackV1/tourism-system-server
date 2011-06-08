<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/ficheDb.php');
	require_once('application/db/champDb.php');

	/**
	 * Classe wsFicheValidation - endpoint du webservice FicheValidation
	 * Gestion des validations à effectuer par les administrateurs
	 * Accessible aux utilisateurs root, superadmin, admin
	 */
	final class wsFicheValidation extends wsEndpoint
	{
		
		/**
		 * Fiches à valider par l'utilisateur courant
		 * Accessible aux utilisateurs root, superadmin, admin
		 * @return array fiches : ficheSimpleCollection collection de ficheSimpleModele
		 */
		protected function _getFichesAValider()
		{
			$this -> restrictAccess('superadmin', 'admin');
			$fiches = ficheValidationDb::getFichesAValider();
			return array('fiches' => $fiches);
		}
		
		
		/**
		 * Champs d'une fiche à valider par l'utilisateur courant
		 * Accessible aux utilisateurs root, superadmin, admin
		 * @param int idFiche : identifiant de la fiche sitFiche.idFiche
		 * @return array champs : champFicheValidationCollection collection de champFicheValidationModele
		 */
		protected function _getChampsFicheAValider($idFiche)
		{
			$this -> restrictAccess('superadmin', 'admin');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$champs = ficheValidationDb::getChampsFicheAValider($oFiche);
			return array('champs' => $champs);
		}
		
		
		/**
		 * Validation d'une valeur de champ d'une fiche
		 * Accessible aux utilisateurs root, superadmin, admin
		 * @param int idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param int idChamp : identifiant du champ sitChamp.idChamp
		 */
		protected function _accepteChampFiche($idFiche, $idChamp)
		{
			$this -> restrictAccess('superadmin', 'admin');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$oChamp = champDb::getChamp($idChamp);
			ficheValidationDb::accepteChampFiche($oFiche, $oChamp);
			return array();
		}
		
		
		/**
		 * Refus d'une valeur de champ d'une fiche
		 * Accessible aux utilisateurs root, superadmin, admin
		 * @param int idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param int idChamp : identifiant du champ sitChamp.idChamp
		 */
		protected function _refuseChampFiche($idFiche, $idChamp)
		{
			$this -> restrictAccess('superadmin', 'admin');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$oChamp = champDb::getChamp($idChamp);
			ficheValidationDb::refuseChampFiche($oFiche, $oChamp);
			return array();
		}
		
	}


?>