<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/ficheFichierModele.php');
	
	// @todo : en verifiant la classe : penser à s'occuper de l'image principale 
	final class ficheFichierDb
	{
	
		const SQL_CREATE_FICHE_FICHIER = "INSERT INTO sitFicheFichier (idFiche) VALUES ('%d')";
		const SQL_UPDATE_FICHE_FICHIER = "UPDATE sitFicheFichier SET md5='%s', nomFichier='%s', path='%s', url='%s', type='%s', extension='%s', principal='%s' WHERE idFichier='%d'";
		const SQL_FICHE_FICHIER = "SELECT idFiche, md5, nomFichier, path, url, type, extension, principal FROM sitFicheFichier WHERE idFichier='%d'";
		const SQL_FICHE_FICHIERS = "SELECT idFichier FROM sitFicheFichier WHERE idFiche='%d'";
		const SQL_DELETE_FICHE_FICHIER = "DELETE FROM sitFicheFichier WHERE idFichier='%d'";		
		
		
		
		
		public static function getFicheFichier($idFichier)
		{
			$fichier = tsDatabase::getRow(self::SQL_FICHE_FICHIER, array($idFichier), DB_FAIL_ON_ERROR);
			
			$oFicheFichier = new ficheFichierModele();
			$oFicheFichier -> setIdFiche($fichier['idFiche']);
			$oFicheFichier -> setIdFichier($idFichier);
			$oFicheFichier -> setMd5($fichier['md5']);
			$oFicheFichier -> setNomFichier($fichier['nomFichier']);
			$oFicheFichier -> setType($fichier['type']);
			$oFicheFichier -> setUrl($fichier['url']);
			$oFicheFichier -> setPath($fichier['path']);
			$oFicheFichier -> setExtension($fichier['extension']);
			$oFicheFichier -> setPrincipal($fichier['principal']);
			
			return $oFicheFichier;
		}
		
		

		public static function getFicheFichiers(ficheModele $oFiche)
		{
			$oFicheFichierCollection = new FicheFichierCollection();
			$idsFichier = tsDatabase::getRecords(self::SQL_FICHE_FICHIERS, array($oFiche -> idFiche));
			foreach($idsFichier as $idFichier)
			{
				$oFichier = self::getFicheFichier($idFichier);
				$oFicheFichierCollection[] = $oFichier -> getObject();
			}
			return $oFicheFichierCollection -> getCollection();
		}
		
		
		
		/**
		 * 
		 * @param ficheModele $oFiche
		 * @param string   $nomFichier
		 * @param string   $principal
		 * @param string   $contentBase64
		 * @return 
		 */
		public static function addFicheFichier(ficheModele $oFiche, $nomFichier, $principal, $url)
		{
			$principalYN = ($membre === true) ? 'Y' : 'N';
			$content = file_get_contents($url);
			
			if ($content === false)
			{
				throw new ApplicationException("Le fichier envoyé n'est pas acccessible");
			}
			$parts = self::explodeFilename($nomFichier);
			$filename = $parts[0];
			$extension = $parts[1];
			$type = self::getType($extension);
			if (is_null($type))
			{
				throw new ApplicationException("Le type de fichier n'est pas correct");
			}
			
			$md5 = md5($content);

			$idFichier = tsDatabase::insert(self::SQL_CREATE_FICHE_FICHIER, array($oFiche -> idFiche));

			$pathFichier = tsConfig::get('TS_PATH_MEDIAS') . $idFichier . '_' . $nomFichier;
			$urlFichier = tsConfig::get('TS_URL_MEDIAS') . $idFichier . '_' . $nomFichier;
			
			tsDatabase::query(self::SQL_UPDATE_FICHE_FICHIER, 
					array($md5, $nomFichier, $pathFichier, $urlFichier, $type, $extension, $principalYN, $idFichier));
			
			file_put_contents($pathFichier, $content);
			
			return $idFichier;
		
		}
		
		
		
		
		public static function deleteFicheFichier(ficheFichierModele $oFicheFichier)
		{
			if (file_exists($oFicheFichier -> path))
			{
				unlink($oFicheFichier -> path);
			}
			return tsDatabase::query(self::SQL_DELETE_FICHE_FICHIER, array($oFicheFichier -> idFichier));
		}
		
		
		
		
		
		/**
		 * Scinde un nom de fichier en Nom / Extension
		 * @return Array : tableau[nom_du_fichier, extension]
		 * @param $filename String : Nom du fichier à analyser
		 */
		private static function explodeFilename($filename)
		{
			$parts = explode('.', $filename);
			$extension = array_pop($parts);
			$arr = implode('/', $parts);
			$name = (count($arr) > 1) ? array_pop($arr) : $arr[0];
			return(array($name, $extension));
		}
		
		
		
		/**
		 * Nettoie le nom de fichier uploadé par l'utilisateur
		 * @return string : le nom du fichier nettoyé
		 * @param $filename string : Nom du fichier
		 * @param $space_replacement string[optional] : caractère de remplacement (vide par défaut)
		 */
		private static function cleanUploadedFilename($filename, $replacedBy = '')
		{
			return(preg_replace('/[^a-zA-Z0-9\.\$\%\'\`\-\@\{\}\~\!\#\(\)\&\_\^]/', 
	  					'', str_replace(array(' ', '%20'), array($replacedBy, $replacedBy),	$filename)));
		}
		
		
		
		private static function getType($extension)
		{
			$type = null;
			
			switch (strtolower($extension))
			{
				case 'pdf':
					$type = 'pdf';
				break;
				case 'jpg': case 'jpeg': case 'png': case 'gif':
					$type = 'image';
				break;
				case 'doc': case 'txt': case 'xls':
				case 'odt': case 'csv':
					$type = 'doc';
				break;
				case 'flv': case 'mpg': case 'mpeg':
				case 'avi': case 'mov': case 'mp4':
					$type = 'video';
				break;
				case 'mp3': case 'wav':
					$type = 'audio';
				break;
			}
			return $type;
		} 
		
		
		
	}
	
	
?>