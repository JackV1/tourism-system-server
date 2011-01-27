<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/ficheDb.php');
	require_once('application/utils/xmlFiche.php');
	require_once('application/utils/tifTools.php');

	/**
	 * Classe wsFicheImport - endpoint du webservice FicheImport
	 * 
	 */
	final class wsFicheImport extends wsEndpoint
	{
		
		/**
		 * Import d'une fiche dans Tourism System
		 * @param string $xmlTif : source XML Tourinfrance de la fiche
		 * @return int idFiche : identifiant numérique de la fiche sitFiche.idFiche
		 * @access root superadmin admin
		 */
		protected function _importFiche($xmlTif)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
		
			$doc = new DOMDocument();
			$doc -> loadXml($xmlTif);
			$xpath = new DOMXPath($doc);
			
			$result = $xpath -> query('//tif:DublinCore/dc:identifier');
			$dcIdentifier = $result -> item(0) -> nodeValue;
			
			
			if(empty($dcIdentifier))
			{
				throw new Exception("Aucun dcIdentifier");
			}
			elseif(is_array($dcIdentifier))
			{
				throw new Exception("Plusieurs dcIdentifier");
			}
			
			$idFiche = ficheDb::getIdFicheByRefExterne($dcIdentifier);
			
			if(empty($idFiche))
			{	
				//$classification = $xmlFiche -> getValue('//tif:DublinCore/tif:Classification/@code');
				$result = $xpath -> query('//tif:DublinCore/tif:Classification/@code');
				$classification = $result -> item(0) -> nodeValue;
				
				$bordereau = tifTools::getBordereau($classification);
				
				
				//$codeInsee = $xmlFiche -> getValue('//tif:Contacts/tif:DetailContact[@type="04.03.13"]//tif:Commune/@code');
				$result = $xpath -> query('//tif:Contacts/tif:DetailContact[@type="04.03.13"]/tif:Adresses/tif:DetailAdresse/tif:Commune/@code');
				$codeInsee = $result -> item(0) -> nodeValue;
				
				$idFiche = ficheDb::createFiche($bordereau, $codeInsee, $dcIdentifier);
				
			}

			$oFiche = ficheDb::getFicheSimpleByIdFiche($idFiche);
			$this -> checkDroitFiche($oFiche, DROIT_ADMIN);
			
			//$raisonSociale = $xmlFiche -> getValue('//tif:Contacts/tif:DetailContact[@type="04.03.13"]//tif:RaisonSociale');
			$result = $xpath -> query('//tif:Contacts/tif:DetailContact[@type="04.03.13"]/tif:RaisonSociale');
			$raisonSociale = $result -> item(0) -> nodeValue;
			
			$queryGps = 'tif:Geolocalisations/tif:DetailGeolocalisation[attribute::type="08.01.02"]/tif:Zone[attribute::type="08.02.07.02"]/tif:Points/tif:DetailPoint[attribute::type="08.02.05.11"]/tif:Coordonnees/tif:DetailCoordonnees[attribute::type="08.02.02.03"]/';
			
			$result = $xpath -> query($queryGps . 'tif:Latitude');
			$gpsLat = ($result -> nodelist -> length > 0) ? $result -> item(0) -> nodeValue : false;
			$result = $xpath -> query($queryGps . 'tif:Longitude');
			$gpsLng = ($result -> nodelist -> length > 0) ? $result -> item(0) -> nodeValue : false;
			

			ficheDb::sauvegardeFicheXml($idFiche, $xmlTif, $raisonSociale, $gpsLat, $gpsLng);
			
			return array('idFiche' => $idFiche);
		}
		
		
		
		
	}


?>