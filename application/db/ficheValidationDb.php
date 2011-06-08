<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/champModele.php');
	require_once('application/modele/ficheSimpleModele.php');
	require_once('application/modele/champFicheValidationModele.php');
	
	/**
	 * Classe d'accès aux données pour la validation des fiches et des champs
	 */
	final class ficheValidationDb
	{
	
		const SQL_FICHES_A_VALIDER = "SELECT idFiche FROM sitFicheVersion WHERE etat='a_valider' AND idFiche IN (%s)";
		const SQL_CHAMPS_FICHE_A_VALIDER = "SELECT idChamp, ancienneValeur, valeur, etat FROM sitFicheVersionChamp WHERE etat='a_valider' AND idFiche='%d' AND idFicheVersion='%d'"; // a valider ?
		const SQL_ACCEPTE_CHAMP = "UPDATE sitFicheVersionChamp SET etat='accepte' WHERE idFiche='%d' AND idChamp='%d' AND etat='a_valider'";
		const SQL_REFUSE_CHAMP = "UPDATE sitFicheVersionChamp SET etat='refuse' WHERE idFiche='%d' AND idChamp='%d' AND etat='a_valider'";
		

		/**
		 * Retourne les fiches à valider par l'utilisateur courant
		 * @return ficheSimpleCollection : collection de ficheSimpleModele
		 */
		public static function getFichesAValider()
		{
			$oFicheSimpleCollection = new ficheSimpleCollection();
			$fichesAdministrables = tsDroits::getFichesAdministrables();
			$sqlFichesIn = "'" . implode("','", $fichesAdministrables) . "'";
			$idsFichesAValider = tsDatabase::getRecords(self::SQL_FICHES_A_VALIDER, array($sqlFichesIn));
			// @todo : est-ce qu'on laisse toutes les fiches à valider sans vérifier le droit de validation sur chacun des champs ?
			// => ne concerne que les admin
			foreach($idsFichesAValider as $idFicheAValider)
			{
				$oFicheSimpleCollection[] = ficheDb::getFicheSimpleByIdFiche($idFicheAValider);
			}
			return $oFicheSimpleCollection;
		}
		
		
		
		/**
		 * Retourne les champs à valider par l'utilisateur courant sur une fiche
		 * @param ficheModele $oFiche : ficheModele
		 * @return champFicheValidationCollection : collection de champFicheValidationModele
		 */
		public static function getChampsFicheAValider(ficheModele $oFiche)
		{
			$oChampFicheValidationCollection = new champFicheValidationCollection();
			$champs = tsDatabase::getRows(self::SQL_CHAMPS_FICHE_A_VALIDER, array($oFiche -> idFiche, $oFiche -> idVersion));
			foreach($champs as $champ)
			{
				$oChamp = champDb::getChamp($idChamp);
				
				if (tsDroits::getDroitFicheChamp($oFiche, $oChamp) & DROIT_VALIDATION)
				{
					$oChampFiche = new ChampFicheValidationModele();
					$oChampFiche -> setIdChamp($champ['idChamp']);
					$oChampFiche -> setAncienneValeur($champ['ancienneValeur']);
					$oChampFiche -> setValeur($champ['valeur']);
					$oChampFiche -> setEtat($champ['etat']);
					$oChampFiche -> setLibelle($oChamp -> libelle);
					$oChampFicheValidationCollection[] = $oChampFiche;
				}
			}
			return $oChampFicheValidationCollection;
		}
		
		
		/**
		 * Accepte le changement d'une valeur de champ
		 * @param ficheModele $oFiche
		 * @param champFicheValidationModele $oChamp
		 */
		public static function accepteChampFiche(ficheModele $oFiche, champFicheValidationModele $oChamp)
		{
			return tsDatabase::query(self::SQL_ACCEPTE_CHAMP, array($oFiche -> idFiche, $oChamp -> idChamp));
		}
		
		
		/**
		 * Refuse le changement d'une valeur de champ
		 * @param ficheModele $oFiche
		 * @param champFicheValidationModele $oChamp
		 */
		public static function refuseChampFiche(ficheModele $oFiche, champFicheValidationModele $oChamp)
		{
			return tsDatabase::query(self::SQL_REFUSE_CHAMP, array($oFiche -> idFiche, $oChamp -> idChamp));			
		}
		
		
	}
	
	
?>