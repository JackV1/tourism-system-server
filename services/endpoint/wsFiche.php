<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
 
	require_once('application/db/ficheDb.php');
	require_once('application/db/communeDb.php');
	
	
	/**
	 * Classe wsFiche - endpoint du webservice Fiche
	 * Service de gestion des fiches (liste, création, suppression)
	 * @access root superadmin admin desk manager
	 */
	final class wsFiche extends wsEndpoint
	{
	
	
		/**
		 * Retourne la source xml de la fiche
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @return string xml : source xml de la fiche au format TIF v3.0
		 * @access root superadmin admin desk manager
		 */
		protected function _getXmlFiche($idFiche)
		{
			//$this -> restrictAccess('root', 'superadmin', 'admin', 'desk', 'manager');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$droitsFiche = tsDroits::getDroitFiche($oFiche);
			$fiche = ficheDb::getFiche($oFiche, $droitsFiche);
			return array('xml' => $fiche -> xml);
		}
		
		
		/**
		 * Retourne un objet ficheModele
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param int $idFicheVersion [optional] : identifiant de la version de la fiche sitFicheVersion.idFicheVersion
		 * @return ficheModele fiche 
		 * @access root superadmin admin desk manager
		 */
		protected function _getFiche($idFiche, $idFicheVersion = null)
		{
			//$this -> restrictAccess('root', 'superadmin', 'admin', 'desk', 'manager');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche, $idFicheVersion);
			if (is_null($idFicheVersion) === false)
			{
				$oFicheSimple = ficheSimpleModele::loadByXml($oFiche -> xml);
				$oFiche -> raisonSociale = $oFicheSimple -> raisonSociale;
				$oFiche -> gpsLat = $oFicheSimple -> gpsLat;
				$oFiche -> gpsLng = $oFicheSimple -> gpsLng;
			}
			$droitsFiche = tsDroits::getDroitFiche($oFiche);
			$fiche = ficheDb::getFiche($oFiche, $droitsFiche);
			return array('fiche' => $fiche);
		}
		
		
		/**
		 * Retourne un objet ficheModele
		 * @param string $reference : identifiant externe de la fiche 
		 * @return ficheModele fiche 
		 * @access root superadmin admin desk manager
		 */
		protected function _getFicheByRefExterne($reference)
		{
			//$this -> restrictAccess('root', 'superadmin', 'admin', 'desk', 'manager');
			$idFiche = ficheDb::getIdFicheByRefExterne($reference);
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$droitsFiche = tsDroits::getDroitFiche($oFiche);
			$fiche = ficheDb::getFiche($oFiche, $droitsFiche);
			return array('fiche' => $fiche);
		}
		
		
		/**
		 * Création d'une fiche
		 * @param string $bordereau : bordereau Tourinfrance de la fiche à créer
		 * @param string $codeInsee : code insee de la commune
		 * @param string $referenceExterne [optional] : référence externe de la fiche
		 * @return int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @access root superadmin admin
		 */
		protected function _createFiche($bordereau, $codeInsee, $referenceExterne = null)
		{
			// @todo:Retourne une fiche simplifiée ?
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$idFiche = ficheDb::createFiche($bordereau, $codeInsee, $referenceExterne);
			return array('idFiche' => $idFiche);
		}
		
		
		/**
		 * Supression d'une fiche
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @access root superadmin admin
		 */
		protected function _deleteFiche($idFiche)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkDroitFiche($oFiche, DROIT_ADMIN);
			ficheDb::deleteFiche($oFiche);
			return array();
		}
		
		
		/**
		 * Retourne la liste des fiches visibles par l'utilisateur courant
		 * @return fiches : ficheCollection (collection de ficheModele)
		 * @access root superadmin admin desk manager
		 */
		protected function _getFiches()
		{
			return array('fiches' => ficheDb::getFiches());
		}
		
		
		/**
		 * Retourne la liste des fiches correspondant aux critères de recherche envoyés
		 * @return fiches : ficheCollection (collection de ficheModele)
		 * @param string $bordereau : code bordereau de filtrage
		 * @param array $filters : filtres de recherche : tableau de codes Tourinfrance
		 * @access root superadmin admin desk manager
		 */
		protected function _rechercheFiches($bordereau = null, $filters = array())
		{

			$fiches = ficheDb::getFiches();
			$hasFilters = (count($filters) > 0);

			foreach($fiches as $k => $fiche)
			{
				$oFiche = ficheDb::getFicheByIdFiche($fiche -> idFiche);
				// Bordereau
				if (is_null($bordereau) === false)
				{
					if ($bordereau != $oFiche -> bordereau)
					{
						unset($fiches[$k]);
					}
				}
				// Filters
				if ($hasFilters)
				{
					$domFiche = new DOMDocument('1.0');
					$domFiche -> loadXML($oFiche -> xml);
					$domXpath = new DOMXpath($domFiche);
					$keep = true;
					
					foreach($filters as $filter)
					{
						$filtersOr = explode('|', $filter);
						$keepTmp = false;
						foreach($filtersOr as &$filterOr)
						{
							$xPathQuery = "//*[@type='".$filterOr."' or @code='".$filterOr."']";
							$keepTmp = $keepTmp || libXml::hasResult($domXpath, $xPathQuery);
						}
						
						$keep = $keep && $keepTmp;
					}
					if ($keep === false)
					{
						unset($fiches[$k]);
					}
				}
			}
			return array('fiches' => $fiches);
		}
		
		
		/**
		 * Méthode de sauvegarde de la fiche
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param stdClass $stdFiche : objet de type stdClass tel que retourné par getFiche
		 * @access root superadmin admin desk manager
		 */
		protected function _sauvegardeFiche($idFiche, $stdFiche)
		{
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$droitsFiche = tsDroits::getDroitFiche($oFiche);
			ficheDb::sauvegardeFiche($oFiche, $stdFiche, $droitsFiche);
			return array();
		}
		
	
		/**
		 * Change l'état de publication d'une fiche
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param bool $publication : le statut de publication
		 * @access root superadmin admin desk manager
		 */
		protected function _setPublicationFiche($idFiche, $publication) //#Anthony
		{
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkDroitFiche($oFiche, DROIT_ADMIN);
			ficheDb::setPublicationFiche($oFiche, $publication);
			return array();
		}
		
	
		/**
		 * Récupère les versions d'une fiche
		 * @return versions : Versions de la fiche
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @access root superadmin admin
		 */
		protected function _getFicheVersions($idFiche)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkDroitFiche($oFiche, DROIT_ADMIN);
			$versions = ficheDb::getFicheVersions($oFiche);
			return array('versions' => $versions);
		}
		
	
		/**
		 * Supprime une version de fiche
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param int $idFicheVersion : identifiant de la version sitFicheVersion.idFicheVersion
		 * @access root superadmin admin
		 */
		protected function _deleteFicheVersion($idFiche, $idFicheVersion)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkDroitFiche($oFiche, DROIT_ADMIN);
			ficheDb::deleteFicheVersion($oFiche, $idFicheVersion);
			return array();
		}
		
	
		/**
		 * Restaure une version de fiche en créant une nouvelle version
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param int $idFicheVersion : identifiant de la version sitFicheVersion.idFicheVersion
		 * @access root superadmin admin
		 */
		protected function _restoreFicheVersion($idFiche, $idFicheVersion)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche, $idFicheVersion);
			$this -> checkDroitFiche($oFiche, DROIT_ADMIN);
			ficheDb::sauvegardeFicheXml($oFiche -> idFiche, $oFiche -> xml);
			return array();
		}
		
		
	}


?>