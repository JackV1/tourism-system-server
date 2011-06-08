<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
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
			
			$xmlFiche = new xmlFiche($xmlTif);
			
			$dcIdentifier = $xmlFiche -> getValue('//tif:DublinCore/dc:identifier');
			
			if (empty($dcIdentifier))
			{
				throw new ImportException("Aucun dcIdentifier");
			}
			elseif (is_array($dcIdentifier))
			{
				$dcIdentifier = $dcIdentifier[0];
			}
			
			/*if (preg_match('/^[A-Z0-9]+$/', $dcIdentifier) == 0)
			{
				throw new ImportException("Le dcIdentifier n'est pas un Code TIF");
			}*/
			
			$idFiche = ficheDb::getIdFicheByRefExterne($dcIdentifier);
			
			$raisonSociale = $xmlFiche -> getValue('//tif:Contacts/tif:DetailContact[attribute::type="04.03.13"][1]/tif:RaisonSociale');
			
			if (empty($raisonSociale))
			{
				throw new ImportException("Aucune raison sociale", $idFiche);
			}
			
			$dcTitle = $xmlFiche -> getValue('//tif:DublinCore/dc:title');
			
			if (empty($dcTitle))
			{
				throw new ImportException("Aucun dcTitle", $idFiche);
			}
			
			$newFiche = false;
			
			if(empty($idFiche))
			{
				$classification = $xmlFiche -> getValue('//tif:DublinCore/tif:Classification/@code');
				$bordereau = tifTools::getBordereau($classification);
				
				$codeInsee = $xmlFiche -> getValue('//tif:Contacts/tif:DetailContact[@type="04.03.13"]//tif:Commune/@code');
				
				$idFiche = ficheDb::createFiche($bordereau, $codeInsee, $dcIdentifier);
				$newFiche = true;
			}

			$oFiche = ficheDb::getFicheSimpleByIdFiche($idFiche);
			$this -> checkDroitFiche($oFiche, DROIT_ADMIN);
			
			ficheDb::createFicheVersion($idFiche, $xmlTif);
			
			return array('idFiche' => $idFiche, 'newFiche' => $newFiche);
		}
		
		
		
		
	}


?>