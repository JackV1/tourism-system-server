<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/utilisateurDb.php');
	require_once('application/db/groupeDb.php');
	require_once('application/db/ficheDb.php');
	require_once('application/db/profilDroitDb.php');
	require_once('application/db/champDb.php');
	require_once('application/db/utilisateurDroitFicheDb.php');

	/**
	 * Classe wsUtilisateurDroitFiche - endpoint du webservice UtilisateurDroitFiche
	 * Gestion des droits sur fiche
	 * @access root superadmin admin
	 */
	final class wsUtilisateurDroitFiche extends wsEndpoint
	{
		
		/**
		 * Retourne les droits sur fiche d'un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @return droitFicheCollection : collection de droitFicheModele
		 * @access root superadmin admin
		 */
		protected function _getDroitsFiche($idUtilisateur)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$droitFiche = utilisateurDroitFicheDb::getDroitsFiche($oUtilisateur);
			return array('droitsFiche' => $droitsFiche);
		}
		
		
		/**
		 * Retourne le droit sur fiche d'un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @return droitFicheModele : droit sur fiche
		 * @access root superadmin admin
		 */
		protected function _getDroitFiche($idUtilisateur, $idFiche)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitFiche($oFiche, DROIT_GET);
			$droitFiche = utilisateurDroitFicheDb::getDroitFiche($oUtilisateur, $oFiche);
			return array('droitFiche' => $droitFiche);
		}
		
		
		/**
		 * Crée ou met à jour un droit sur fiche pour un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param droitFicheModele $droit : droit sur la fiche
		 * @access root superadmin admin
		 */
		protected function _setDroitFiche($idUtilisateur, $idFiche, $droit)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oFiche = ficheDb::getFicheSimpleByIdFiche($idFiche);
			$oDroit = baseModele::getInstance($stdDroit, 'droitFicheModele');
			
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitFiche($oFiche, DROIT_ADMIN);
			
			utilisateurDroitFicheDb::setDroitFiche($oUtilisateur, $oFiche, $oDroit);
			return array();
		}
		
		
		/**
		 * Crée ou met à jour un droit sur fiche - champ pour un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param int $idChamp : identifiant de champ sitChamp.idChamp
		 * @param droitFicheModele $droit : droit sur la fiche
		 * @access root superadmin admin
		 */
		protected function _setDroitFicheChamp($idUtilisateur, $idFiche, $idChamp, $droit)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oFiche = ficheDb::getFicheSimpleByIdFiche($idFiche);
			$oChamp = champDb::getChamp($idChamp);
			$oDroit = baseModele::getInstance($stdDroit, 'droitChampModele');
			
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitFiche($oFiche, DROIT_ADMIN);
			$this -> checkDroitChamp($oChamp, DROIT_GET);
		
			utilisateurDroitFicheDb::setDroitFicheChamp($oUtilisateur, $oFiche, $oChamp, $oDroit);
			return array();
		}
		
		
		/**
		 * Retourne le droit sur fiche - champ d'un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param int $idChamp : identifiant de champ sitChamp.idChamp
		 * @return droitFicheChampModele droitFicheChamp : droit sur la fiche - champ
		 * @access root superadmin admin
		 */
		/*protected function _getDroitFicheChamp($idUtilisateur, $idFiche, $idChamp)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oFiche = ficheDb::getFiche($idFiche);
			$oChamp = champDb::getChamp($idChamp);
			
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitFiche($oFiche, DROIT_GET);
			$this -> checkDroitChamp($oChamp, DROIT_GET);
			
			$droitFicheChamp = utilisateurDroitFicheDb::getDroitFicheChamp($oUtilisateur, $oFiche, $oChamp);
			return array('droitFicheChamp' => $droitFicheChamp);
		}*/
		
		
		/**
		 * Supprime un droit sur fiche pour un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @access root superadmin admin
		 */
		protected function _deleteDroitFiche($idUtilisateur, $idFiche)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oFiche = ficheDb::getFicheSimpleByIdFiche($idFiche);
			
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitFiche($oFiche, DROIT_ADMIN);
			
			utilisateurDroitFicheDb::deleteDroitFiche($oUtilisateur, $oFiche);
			return array();
		}
		
		
		/**
		 * Supprime un droit sur fiche - champ pour un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param int $idChamp : identifiant de champ sitChamp.idChamp
		 * @access root superadmin admin
		 */
		protected function _deleteDroitFicheChamp($idUtilisateur, $idFiche, $idChamp)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oFiche = ficheDb::getFicheSimpleByIdFiche($idFiche);
			$oChamp = champDb::getChamp($idChamp);
						
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitFiche($oFiche, DROIT_ADMIN);
			$this -> checkDroitChamp($oChamp, DROIT_GET);
			
			utilisateurDroitFicheDb::deleteDroitFicheChamp($oUtilisateur, $oFiche, $oChamp);
			return array();
		}
		
		
		/**
		 * Associe un droit sur fiche d'un utilisateur à un profil de droits
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param int $idProfil : identifiant de profil sitProfil.idProfil
		 * @access root superadmin admin
		 */
		protected function _setDroitFicheProfil($idUtilisateur, $idFiche, $idProfil)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oFiche = ficheDb::getFicheSimpleByIdFiche($idFiche);
						
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitFiche($oFiche, DROIT_ADMIN);
			
			if (is_null($idProfil) === false)
			{
				$oProfil = profilDroitDb::getProfil($idProfil);
				$this -> checkDroitProfil($oProfil, DROIT_GET);
			}
			
			utilisateurDroitFicheDb::setDroitFicheProfil($oUtilisateur, $oFiche, $oProfil);
			return array();
		}
		
	}


?>