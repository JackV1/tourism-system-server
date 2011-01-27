<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/thesaurusDb.php');
	require_once('application/db/communeDb.php');
	
	/**
	 * Classe wsThesaurus - endpoint du webservice Thesaurus
	 * Gestion des thésaurus
	 */
	final class wsThesaurus extends wsEndpoint
	{
	
		/**
		 * Méthode de créaton d'un thésaurus local
		 * @param string $codeThesaurus : identifiant de thésaurus à créer
		 * 			code thésaurus sous la forme MTH.LOC.XXX 
		 * @param string $libelle : libellé du thésaurus à créer
		 * @return int prefixe : préfixe numérique pour la codification TourinFrance
		 * @access root superadmin admin desk manager
		 */
		protected function _createThesaurus($codeThesaurus, $libelle)
		{
			$this -> restrictAccess('root');
			$prefixe = thesaurusDb::createThesaurus($codeThesaurus, $libelle);
			return array('prefixe' => $prefixe);
		}
		
		/**
		 * Retourne la liste des thésaurii visibles de l'utilisateur
		 * @return thesaurusCollection thesaurii : collection de thesaurusModele
		 * @access root
		 */
		protected function _getThesaurii()
		{
			// @todo : ouvrir cette méthode à d'autres utilisateurs
			$this -> restrictAccess('root');
			$thesaurii = thesaurusDb::getThesaurii();
			return array('thesaurii' => $thesaurii);
		}
		
		/**
		 * Mise à jour du libellé et du codeThesaurus
		 * @param string $codeThesaurus : identifiant de thésaurus
		 * @param string $libelle : libellé du thésaurus
		 * @access root
		 */
		protected function _updateThesaurus($codeThesaurus, $libelle)
		{
			$this -> restrictAccess('root');
			$oThesaurus = thesaurusDb::getThesaurus($codeThesaurus);
			$this -> checkDroitThesaurus($oThesaurus, DROIT_ADMIN);
			thesaurusDb::updateThesaurus($oThesaurus, $codeThesaurus, $libelle);
			return array();
		}
		
		/**
		 * Retourne les entrées d'un thésaurus dans une langue
		 * @param string $codeThesaurus : identifiant de thésaurus
		 * @param string $codeLangue : code langue ISO 639-1
		 * @return array entreeThesaurus : [] = array('cle', 'libelle', 'liste')
		 * @access root superadmin admin desk manager
		 */
		protected function _getEntreesThesaurus($codeThesaurus, $codeLangue)
		{
			$oThesaurus = thesaurusDb::getThesaurus($codeThesaurus);
			$this -> checkDroitThesaurus($oThesaurus, DROIT_GET);
			$entreesThesaurus = thesaurusDb::getEntreesThesaurus($oThesaurus, $codeLangue);
			return array('entreesThesaurus' => $entreesThesaurus);
		}
		
		/**
		 * Ajoute une entrée à un thésaurus local
		 * @param string $codeThesaurus : identifiant de thésaurus
		 * @param string $cleParente : clé de la liste parente (MTH.NAT.TIFv30)
		 * @param string $libelle : libellé à ajouter à la liste
		 * @param object $codeLangue [optional] : code langue ISO 639-1 (fr par défaut)
		 * @return string codeTif : code Tourinfrance de l'entrée créée (101.02.01.02.01)
		 * @access root
		 */
		protected function _addEntreeThesaurus($codeThesaurus, $cleParente, $libelle, $codeLangue = 'fr')
		{
			$this -> restrictAccess('root');
			$oThesaurus = thesaurusDb::getThesaurus($codeThesaurus);
			$this -> checkDroitThesaurus($oThesaurus, DROIT_GET);
			$codeTif = thesaurusDb::addEntreeThesaurus($oThesaurus, $cleParente, $libelle, $codeLangue);
			return array('codeTif' => $codeTif);
		}
		
		/**
		 * Supprime une entrée d'un thésaurus local
		 * @param string $codeTIF : code Tourinfrance à supprimer
		 * @access root
		 */
		protected function _deleteEntreeThesaurus($codeTIF)
		{
			// @todo : ajouter thésaurus pour effectuer le controle
			$this -> restrictAccess('root');
			thesaurusDb::deleteEntreeThesaurus($codeTIF);
			return array();
		}
		
		/**
		 * Ajoute dans une nouvelle langue ou renomme dans une langue existante 
		 * une entrée de thésaurus local
		 * @param string $codeTIF : code Tourinfrance à supprimer
		 * @param string $codeLangue : code langue ISO 639-1
		 * @param string $libelle : libellé dans la langue spécifiée
		 * @access root
		 */
		protected function _setEntreeThesaurus($codeTIF, $codeLangue, $libelle)
		{
			// @todo : ajouter thésaurus pour effectuer le controle
			$this -> restrictAccess('root');
			thesaurusDb::setEntreeThesaurus($codeTIF, $codeLangue, $libelle);
			return array();
		}
		
		/**
		 * Retourne une liste d'entrées de thésaurus
		 * @param string $liste : liste à retourner
		 * @param string $cle : expression régulière de la clé (02.01.01.*)
		 * @access all
		 */
		protected function _getListeThesaurus($liste, $cle, $pop)
		{
			$liste = thesaurusDb::getListeThesaurus($liste, $cle, $pop);
			return array('liste' => $liste);
		}
		
		/**
		 * Retourne une partie du thésaurus sous forme d'un arbre
		 * @param string $cle : expression régulière de la clé (02.01.01.*)
		 * @access all
		 */
		protected function _getArbreThesaurus($cle, $pop)
		{
			$arbre = thesaurusDb::getArbreThesaurus($cle, $pop);
			return array('arbre' => $arbre);
		}
		
	}


?>