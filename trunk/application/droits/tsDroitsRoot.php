<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class tsDroitsRoot extends tsDroitsDefault implements tsDroitsInterface
	{
		
		const SQL_UTILISATEURS = "SELECT idUtilisateur FROM sitUtilisateur";
		
		const SQL_TERRITOIRES = "SELECT idTerritoire FROM sitTerritoire";
		
		const SQL_DROIT_FICHE = "SELECT idFiche FROM sitFiche";
		
		//const SQL_DROIT_FICHE = "SELECT idFiche FROM sitFiche";
		
		
		public function loadDroits()
		{
			$this -> loadGroupeUtilisateur();
			$this -> loadDroitsTerritoire();
			$this -> loadDroitsFiche();
			$this -> loadUtilisateursAdministrables();
		}
		
		
		/**
		 * Chargement des utilisateurs administrables 
		 */
		protected function loadUtilisateursAdministrables()
		{
			$sql = constant(get_class($this) . '::SQL_UTILISATEURS');
			$this -> utilisateursAdministrables = tsDatabase::getRecords($sql,  array($this -> idUtilisateur));
		}
		
		
		public function getDroitFicheChamp(ficheModele $oFiche, champModele $oChamp)
		{
			// @todo : droitFicheModele
			$droit = new droitChampModele();
			$droit -> setVisualisation(true);
			$droit -> setValidation(true);
			$droit -> setModification(true);
			return $droit -> getDroit();
		}
		
		public function getDroitFiche(ficheModele $oFiche)
		{
			$droit = new droitFicheModele();
			$droit -> setVisualisation(true);
			$droit -> setModification(true);
			$droit -> setValidation(true);
			$droit -> setSuppressionFiches(true);
			return $droit -> getDroit();
		}
		
		
		public function getDroitThesaurus(thesaurusModele $oThesaurus)
		{
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
		public function getDroitUtilisateur(utilisateurModele $oUtilisateur)
		{
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
		public function getDroitProfil(profilModele $oProfil)
		{
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
		public function getDroitTerritoire(territoireModele $oTerritoire)
		{
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
		
		public function getDroitBordereauTerritoire(bordereauModele $oBordereau, territoireModele $oTerritoire)
		{
			$droit = new droitTerritoireModele();
			$droit -> setVisualisation(true);
			$droit -> setModification(true);
			$droit -> setValidation(true);
			$droit -> setSuppressionFiches(true);
			$droit -> setCreationFiches(true);
			$droit -> setAdministration(true);
			return $droit -> getDroit();
			
		}
		
		public function getDroitBordereauCommune(bordereauModele $oBordereau, communeModele $oCommune)
		{
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
		public function getDroitGroupe(groupeModele $oGroupe)
		{
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
		public function getDroitChamp(champModele $oChamp)
		{
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
	}
	

?>