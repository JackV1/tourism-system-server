<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/communeModele.php');
	
	final class communeDb
	{
	
		//const SQL_COMMUNE = "SELECT codeInsee, codePostal, libelle, gpsLat, gpsLng FROM sitCommune WHERE codeInsee='%d'";
		const SQL_COMMUNE = "SELECT codeInsee, codePostal, libelle, gpsLat, gpsLng FROM sitCommune WHERE codeInsee='%s'";
		
		public static function getCommune($codeInsee)
		{
			//if (is_numeric($codeInsee) === false)
			if (!preg_match('/^([0-9])([A-B0-9])([0-9]{3})$/i',$codeInsee))
			{
				throw new ApplicationException("Le code insee passé en paramètre n'est pas numérique");
			}
			$result = tsDatabase::getRows(self::SQL_COMMUNE, array($codeInsee));
			if (count($result) != 1)
			{
				throw new ApplicationException("La commune $codeInsee n'existe pas");
			}
			
			$oCommune = new communeModele();
			$oCommune -> setLibelle($result[0]['libelle']);
			$oCommune -> setCodePostal($result[0]['codePostal']);
			$oCommune -> setCodeInsee($result[0]['codeInsee']);
			$oCommune -> setGpsLat($result[0]['gpsLat']);
			$oCommune -> setGpsLng($result[0]['gpsLng']);
			
			return $oCommune;
		}

		
	}
	
	
?>