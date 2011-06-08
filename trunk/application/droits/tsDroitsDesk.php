<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class tsDroitsDesk extends tsDroitsDefault implements tsDroitsInterface
	{
		
		
		protected function loadUtilisateursAdministrables() {}
		
		protected function loadTerritoiresAdministrables() {}
		
		
		// getDroitFicheChamp ??
		
		
		public function getDroitGroupe(groupeModele $oGroupe)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		public function getDroitChamp(champModele $oChamp)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		public function getDroitFicheChamp(ficheModele $oFiche, champModele $oChamp)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		public function getDroitUtilisateur(utilisateurModele $oUtilisateur)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		public function getDroitProfil(profilModele $oProfil)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		public function getDroitTerritoire(territoireModele $oTerritoire)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		public function getDroitThesaurus(thesaurusModele $oThesaurus)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		public function getDroitBordereauTerritoire(bordereauModele $oBordereau, territoireModele $oTerritoire)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		public function getDroitBordereauCommune(bordereauModele $oBordereau, communeModele $oCommune)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
	}


?>