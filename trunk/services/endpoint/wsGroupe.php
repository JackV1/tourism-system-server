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
	require_once('application/db/territoireDb.php');

	/**
	 * Classe wsGroupe - endpoint du webservice Groupe
	 * Gestion des groupes d'utilisateurs dans Tourism System
	 * @access root superadmin admin
	 */
	final class wsGroupe extends wsEndpoint
	{
	
		/**
		 * Création d'un groupe d'utilisateurs
		 * @param string $nomGroupe : nom du groupe à créer
		 * @return int idGroupe : identifiant du groupe sitGroupe.idGroupe 
		 * @access root
		 */
		protected function _createGroupe($nomGroupe)
		{
			$this -> restrictAccess('root');
			$idGroupe = groupeDb::createGroupe($nomGroupe);
			return array('idGroupe' => $idGroupe);
		}
		
		
		/**
		 * Suppression d'un groupe d'utilisateurs
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @access root
		 */
		protected function _deleteGroupe($idGroupe)
		{
			$this -> restrictAccess('root');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$this -> checkDroitGroupe($oGroupe, DROIT_DELETE);
			groupeDb::deleteGroupe($oGroupe);
			return array();
		}
		
		/**
		 * Définit l'utilisateur administrateur d'un groupe
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @access root
		 */
		protected function _setSuperAdminGroupe($idGroupe, $idUtilisateur)
		{
			$this -> restrictAccess('root');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$this -> checkDroitGroupe($oGroupe, DROIT_ADMIN);
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			groupeDb::setSuperAdminGroupe($oGroupe, $oUtilisateur);
			return array();
		}
		
		/**
		 * Retourne la liste des groupes d'utilisateurs
		 * @return groupeCollection groupes : collection de groupeModele
		 * @access root
		 */
		protected function _getGroupes()
		{
			$this -> restrictAccess('root');
			$groupes = groupeDb::getGroupes();
			return array('groupes' => $groupes);
		}
		
		/**
		 * Retourne la liste des utilisateurs d'un groupe
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @return array utilisateurs : tableau de sitUtilisateur.idUtilisateur
		 * @access root superadmin admin
		 */
		protected function _getUtilisateursGroupe($idGroupe)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$this -> checkDroitGroupe($oGroupe, DROIT_GET);
			$utilisateurs = groupeDb::getUtilisateursGroupe($oGroupe);
			return array('utilisateurs' => $utilisateurs);
		}
		
		/**
		 * Récupération de tous les territoires d'un groupe
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @access root
		 */
		protected function _getGroupeTerritoires($idGroupe)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$this -> checkDroitGroupe($oGroupe, DROIT_GET);
			$territoires = groupeDb::getGroupeTerritoires($oGroupe);
			return array('territoires' => $territoires);
		}
		
		/**
		 * Liaison d'un groupe à un territoire
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @param int $idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @access root
		 */
		protected function _addGroupeTerritoire($idGroupe, $idTerritoire)
		{
			$this -> restrictAccess('root');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$this -> checkDroitGroupe($oGroupe, DROIT_ADMIN);
			$this -> checkDroitTerritoire($oTerritoire, DROIT_GET);
			groupeDb::addGroupeTerritoire($oGroupe, $oTerritoire);
			return array();
		}
		
		/**
		 * Suppression de la liaison d'un groupe à un territoire
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @param int $idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @access root
		 */
		protected function _deleteGroupeTerritoire($idGroupe, $idTerritoire)
		{
			$this -> restrictAccess('root');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$this -> checkDroitGroupe($oGroupe, DROIT_ADMIN);
			$this -> checkDroitTerritoire($oTerritoire, DROIT_GET);
			groupeDb::deleteGroupeTerritoire($oGroupe, $oTerritoire);
			return array();
		}
		
		/**
		 * Suppression de la liaison d'un groupe à un territoire
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @param int $idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @access root
		 */
		protected function _updateGroupe($idGroupe, $nomGroupe)
		{
			$this -> restrictAccess('root');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$this -> checkDroitGroupe($oGroupe, DROIT_ADMIN);
			groupeDb::updateGroupe($oGroupe, $nomGroupe);
			return array();
		}
		
	}


?>