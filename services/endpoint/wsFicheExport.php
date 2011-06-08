<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/ficheDb.php');

	/**
	 * Classe wsFicheExport - endpoint du webservice FicheExport
	 * 
	 */
	final class wsFicheExport extends wsEndpoint
	{
		
		/**
		 * Export d'une fiche Tourinfrance depuis Tourism System
		 * @param string $xmlTif : source XML Tourinfrance de la fiche
		 * @return int idFiche : identifiant numérique de la fiche sitFiche.idFiche
		 */
		protected function _exportFicheTourinFrance($codeTif)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$droitsFiche = tsDroits::getDroitsFiche($idFiche);
			$idFiche = ficheDb::getXmlFiche($idFiche, $droitsFiche);
			return array('idFiche' => $idFiche);
		}
		
		
		
		
	}


?>