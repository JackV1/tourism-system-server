<?php

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	class libXml
	{
		
		

		public static function JSONtoXML(DOMDocument $domFiche, champModele $oChamp, $json, $base = '')
		{
			// On efface tout le noeud
			$xpath = new DOMXPath($domFiche);
			$nodelist = $xpath -> query($xpathbase . $oChamp -> xPath);
			
			for ($i = 0; $i < $nodelist -> length; $i++)
			{
				$node = $nodelist -> item($i);
				$node -> parentNode -> removeChild($node);
				$domFiche -> saveXML();
			}
			$domFiche -> saveXML();
			
			foreach($json as $itemnumber => $arritem)
			{
				foreach($oChamp -> champs as $key => $value)
				{
					$xpathQuery = $xpathbase . $oChamp -> xPath . '['. intval($itemnumber + 1) .']/' . $value -> xPath;
					
					$v = str_replace('<br />', "\n", $arritem[$value -> identifiant]);
					$v = str_replace('u00e9', 'é', $v);
					$v = str_replace('u00e8', 'è', $v);
					$v = str_replace('u00e0', 'à', $v);
					$v = str_replace('u00e2', 'â', $v);
					$v = str_replace('u00e7', 'ç', $v);
					$v = str_replace('u00ea', 'ê', $v);
					$v = str_replace('u00f4', 'ô', $v);
					$v = str_replace('u00fb', 'û', $v);
					$v = str_replace('u00f9', 'ù', $v);
					
					// Jermey
					if ($v != '')
					{
						$domFiche -> setValueFromXPath($xpathQuery, $v);
					}
					
					$domFiche -> saveXML();
				}
			}
			
			$domFiche -> saveXML();
			return $domFiche;
		}
		
		
		
		
		public static function getXpathValue($domXpath, $oChamp, $node = null)
		{

			if (count($oChamp -> champs) > 0)
			{
				$retour = array();
				$nodelist = $domXpath -> query($oChamp -> xPath);
				for($i = 0; $i < $nodelist -> length; $i++)
				{
					foreach($oChamp -> champs as $champ)
					{
						$domNode = $nodelist -> item($i);
						$retour[$i][$champ -> identifiant] = libXml::getXpathValue($domXpath, $champ, $domNode);
					}
				}
				return $retour;
			}
			
			$nodelist = (is_null($node)) ? $domXpath -> query($oChamp -> xPath) :
											$domXpath -> query($oChamp -> xPath, $node);

			if ($nodelist -> length == 0)
			{
				$retour = '';
			}
			elseif($nodelist -> length == 1)
			{
				$retour = $nodelist -> item(0) -> nodeValue;
			}
			else
			{
				$retour = array();
				for ($i = 0; $i < $nodelist -> length; $i++)
				{
					$retour[] = $nodelist -> item($i) -> nodeValue;
				}
			}
			return $retour;
		}
		
		
		
		
		public static function hasResult($domXpath, $query)
		{
			$nodelist = $domXpath -> query($query);
			return ($nodelist -> length > 0);
		}
		
		
		
		
	}
	
?>