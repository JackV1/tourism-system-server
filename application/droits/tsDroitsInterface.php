<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	interface tsDroitsInterface
	{

		public function getDroitGroupe(groupeModele $oGroupe);
		
		public function getDroitChamp(champModele $oChamp);
		
		public function getDroitFicheChamp(ficheModele $oFiche, champModele $oChamp);
		
		public function getDroitFiche(ficheModele $oFiche);
		
		public function getDroitUtilisateur(utilisateurModele $oUtilisateur);
		
		public function getDroitProfil(profilModele $oProfil);
		
		public function getDroitTerritoire(territoireModele $oTerritoire);
		
		public function getDroitThesaurus(thesaurusModele $oThesaurus);
		
		public function getDroitBordereauTerritoire(bordereauModele $oBordereau, territoireModele $oTerritoire);
		
		public function getDroitBordereauCommune(bordereauModele $oBordereau, communeModele $oCommune);
		
	}

?>