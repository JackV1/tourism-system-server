<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class tsDroitsSuperAdmin extends tsDroitsAdmin implements tsDroitsInterface
	{
		
		const SQL_UTILISATEURS = "SELECT su.idUtilisateur FROM sitUtilisateur su, sitUtilisateur su2 
								WHERE su.idGroupe=su2.idGroupe AND su2.idUtilisateur='%d'";

		const SQL_PROFILS_ADMINISTRABLES = "SELECT idProfil FROM sitProfilDroit WHERE idGroupe='%d'";
		
		private $profilsGroupe = null;
		
		

		public function getDroitGroupe(groupeModele $oGroupe)
		{
			if ($oGroupe -> idGroupe != $this -> idGroupe)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			return DROIT_GET | DROIT_ADMIN;
		}


		public function getDroitChamp(champModele $oChamp)
		{
			return DROIT_GET;
		}
		
		
		public function getDroitFiche(ficheModele $oFiche)
		{
			$idFiche = $oFiche -> getIdFiche();
			//assert('in_array($idFiche, $this -> fichesAdministrables)');
			$droit = new droitFicheModele();
			$droit -> setVisualisation(true);
			$droit -> setModification(true);
			$droit -> setValidation(true);
			$droit -> setSuppressionFiches(true);
			return $droit -> getDroit();
		}
		
		
		public function getDroitFicheChamp(ficheModele $oFiche, champModele $oChamp)
		{
			$idFiche = $oFiche -> getIdFiche();
			//assert('in_array($idFiche, $this -> fichesAdministrables)');
			$droit = new droitChampModele();
			$droit -> setVisualisation(true);
			$droit -> setModification(true);
			$droit -> setValidation(true);
			return $droit -> getDroit();
		}
		
		
		public function getDroitUtilisateur(utilisateurModele $oUtilisateur)
		{
			$idUtilisateur = $oUtilisateur -> getIdUtilisateur();
			//assert('in_array($idUtilisateur, $this -> utilisateursAdministrables)');
			if (in_array($idUtilisateur, $this -> utilisateursAdministrables) === false)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
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
			
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
		
		public function getDroitTerritoire(territoireModele $oTerritoire)
		{
			$idTerritoire = $oTerritoire -> getIdTerritoire();
			//assert('in_array($idTerritoire, $this -> territoiresAdministrables)');
			if (in_array($idTerritoire, $this -> territoiresAdministrables) === false)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			return DROIT_GET | DROIT_ADMIN;
		}
		
		
		public function getDroitThesaurus(thesaurusModele $oThesaurus)
		{
			return DROIT_GET;
		}
		
		
		public function getDroitBordereauCommune(bordereauModele $oBordereau, communeModele $oCommune)
		{
			$codeInsee = $oCommune -> getCodeInsee();
			$bordereau = $oBordereau -> getBordereau();
			
			//assert('isset($this -> droitsBordereauCommune[$bordereau][$codeInsee])');
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
			//assert('isset($this -> droitsBordereauTerritoire[$bt])');
			if (isset($this -> droitsBordereauTerritoire[$bt]) === false)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			return $this -> droitsBordereauTerritoire[$bt];
		}
		
	}



?>