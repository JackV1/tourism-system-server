<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
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
			if (!preg_match('/^([0-9])([A-B0-9])([0-9]{3})$/i',$codeInsee))
			{
				throw new ApplicationException("Le code insee passé en paramètre n'est pas numérique");
			}
			$result = tsDatabase::getObject(self::SQL_COMMUNE, array($codeInsee));
			$oCommune = communeModele::getInstance($result, 'communeModele');
			return $oCommune;
		}

		
	}
	
	
?>