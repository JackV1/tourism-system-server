<?php

/**
 * @version		0.3 alpha-test - 2013-01-25
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/ficheDb.php');
	require_once('application/db/maintenanceDb.php');
	require_once('application/utils/xmlFiche.php');

	/**
	 * Classe wsMaintenance - endpoint du webservice Maintenance
	 * 
	 */
	final class wsMaintenance extends wsEndpoint
	{
		
		/**
		 * Purge des Manifs dans Tourism System
		 * @return int idFiche : identifiant numérique de la fiche sitFiche.idFiche
		 * @access root superadmin admin
		 */
		protected function _purgeFMA()
		{
			$this -> restrictAccess();
			
			//Initialisation des variables utiles
			$today = strtotime(date('m/d/Y', time()));
			
			//Obtenir les fiches administrables
			$oFiches = ficheDb::getFiches();
			
			//Parcourir les fiches, pour chacune extraire les idFiches
			$idFiches = array();
			foreach($oFiches as $oFiche)
			{
				if ($oFiche -> bordereau == "FMA")
				{
					$idFiches[] = $oFiche -> idFiche;
				}
			}
			
			//Parcourir les idFiches, aller chercher le XML
			$fichesASupprimer = array();
			foreach($idFiches as $idFiche)
			{
				$oFiche = ficheDb::getFicheByIdFiche($idFiche, null);
				$xmlFiche = new xmlFiche($oFiche -> xml);
				$dateFin = $xmlFiche -> getValue('//tif:DetailPeriode[attribute::type="09.01.05"][1]/tif:Dates/tif:DetailDates/tif:DateFin');
				$arrDateFin = explode('-',$dateFin);
				
				$dateFin = strtotime($arrDateFin[1] . "/" . $arrDateFin[2] . "/" . $arrDateFin[0]);
				
				if ($dateFin < $today)
				{
					$fichesASupprimer[] = $idFiche;
				}
			}
			//Supprimer la fiche dont aujourd'hui > date de final
			foreach($fichesASupprimer as $idFiche)
			{
				$oFiche = ficheDb::getFicheByIdFiche($idFiche);
				$this -> checkDroitFiche($oFiche, DROIT_SUPPRESSION_FICHES);
				ficheDb::deleteFiche($oFiche);
			}

			return array('reponse' => $fichesASupprimer);
		}
		
		/**
		 * Purge de la table sitSessions
		 * @access root
		 */
		protected function _purgeSessions()
		{
			$this -> restrictAccess('root');
			maintenanceDb::purgeSessions();
			return array();
		}
		
		
	}


?>