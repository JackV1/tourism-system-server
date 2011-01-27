<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class tsDroitsAdmin extends tsDroitsDefault implements tsDroitsInterface
	{

		const SQL_UTILISATEURS_FICHE = "SELECT su.idUtilisateur FROM sitUtilisateur su, sitUtilisateurDroitFiche sud
					WHERE su.idGroupe='%d' AND su.idUtilisateur=sud.idUtilisateur AND sud.idFiche='%d'";
		
		const SQL_BORDEREAU_TERRITOIRE = "SELECT bordereau, idTerritoire, idDroit FROM sitUtilisateurDroit WHERE 
					idUtilisateur='%d' AND bordereau IS NOT NULL AND idTerritoire IS NOT NULL";
		
		const SQL_FICHES_BORDEREAU_TERRITOIRE = "SELECT idFiche FROM sitFiche WHERE bordereau='%s' AND codeInsee='%s'";
		
		const SQL_FICHES_UTILISATEUR = "SELECT DISTINCT(idFiche) FROM sitUtilisateurDroitFiche WHERE idUtilisateur='%d'";
		
		const SQL_BORDEREAU_COMMUNE_FICHE = "SELECT bordereau, codeInsee FROM sitFiche WHERE idFiche='%d'";
		
		protected $droitsFiche = array();
		protected $droitsBordereauCommune = array();
		

		protected function loadUtilisateursAdministrables()
		{
			$utilisateurs = array();
			foreach($this -> fichesAdministrables as $idFiche)
			{
				$utilisateurs = array_merge($utilisateurs, tsDatabase::getRecords(self::SQL_UTILISATEURS_FICHE, array($this -> idGroupe, $idFiche)));
			}
			
			$this -> utilisateursAdministrables = array_unique($utilisateurs);
		}
		
		
		
		public function getDroitTerritoire(territoireModele $oTerritoire)
		{
			$idTerritoire = $oTerritoire -> getIdTerritoire();
			assert('in_array($idTerritoire, $this -> territoiresAdministrables)');
			if (in_array($idTerritoire, $this -> territoiresAdministrables) === false)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			return DROIT_GET;
		}
		
		
		public function getDroitThesaurus(thesaurusModele $oThesaurus)
		{
			return DROIT_GET;
		}
		
		
		public function getDroitBordereauCommune(bordereauModele $oBordereau, communeModele $oCommune)
		{
			$codeInsee = $oCommune -> getCodeInsee();
			$bordereau = $oBordereau -> getBordereau();
			
			assert('isset($this -> droitsBordereauCommune[$bordereau][$codeInsee])');
			if (isset($this -> droitsBordereauCommune[$bordereau][$codeInsee]) === false)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			return $this -> droitsBordereauCommune[$bordereau][$codeInsee];
		}
		
		
		public function getDroitBordereauTerritoire(bordereauModele $oBordereau, territoireModele $oTerritoire)
		{
			$idTerritoire = $oTerritoire -> getIdTerritoire();
			$bordereau = $oBordereau -> getBordereau();
			$bt = $bordereau . $idTerritoire;
			assert('isset($this -> droitsBordereauTerritoire[$bt])');
			if (isset($this -> droitsBordereauTerritoire[$bt]) === false)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			return $this -> droitsBordereauTerritoire[$bt];
		}
		
		
		
		public function getDroitGroupe(groupeModele $oGroupe)
		{
			if ($oGroupe -> idGroupe != $this -> idGroupe)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			return DROIT_GET;
		}
		
		
		public function getDroitChamp(champModele $oChamp)
		{
			return DROIT_GET;
		}
		
		
		public function getDroitProfil(profilModele $oProfil)
		{
			if (is_null($this -> profilsGroupe))
			{
				$this -> profilsGroupe = tsDatabase::getRecords(self::SQL_PROFILS_ADMINISTRABLES, array($this -> idGroupe));
			}
			
			if (in_array($oProfil -> idProfil, $this -> profilsGroupe) === false)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			
			return DROIT_GET;
		}
		
		
		
		public function getDroitUtilisateur(utilisateurModele $oUtilisateur)
		{
			$idUtilisateur = $oUtilisateur -> getIdUtilisateur();
			assert('in_array($idUtilisateur, $this -> utilisateursAdministrables)');
			if (in_array($idUtilisateur, $this -> utilisateursAdministrables) === false)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			
			// Droit d'administration
			$oDroit = new droitModele();
			$oDroit -> setAdministration(true);
			$intDroit = $oDroit -> getDroit();
			
			$droit = $intDroit;
			// Récupération des fiches administrables par l'utilisateur
			$fiches = tsDatabase::getRows(self::SQL_FICHES_UTILISATEUR, array($idUtilisateur));
			foreach($fiches as $fiche)
			{
				// Commune - bordereau de chacune des fiches -> déduction des droits
				$infoFiche = tsDatabase::getRecord(self::SQL_BORDEREAU_COMMUNE_FICHE, array($idUtilisateur));
				$bordereau = $infoFiche['bordereau'];
				$codeInsee = $infoFiche['codeInsee'];
				$droit = $droit & $this -> droitsBordereauCommune[$bordereau][$codeInsee];
			}
			
			$oDroitAdmin = new droitModele();
			$oDroitAdmin -> loadDroit($droit);
			$droitAdmin = $oDroitAdmin -> getAdministration();
			return ($droitAdmin) ? DROIT_GET | DROIT_ADMIN | DROIT_DELETE : DROIT_GET;
		}
		
		

	
	}

?>