<?php

/**
 * @version		0.3 alpha-test - 2013-01-25
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	class xmlFiche
	{

		private $dom;
		private $xml;
		private $xpath;
		private $xsltProc;

		public function __construct(&$xml)
		{
			$this -> xml = &$xml;
			$this -> dom = new DOMDocument("1.0");
			$this -> dom -> loadXml($this -> xml);

			$this -> xpath = new DOMXPath($this -> dom);
			$this -> xsltProc = new XSLTProcessor();
		}


		public function &getXml()
		{
			return $this -> xml;
		}


		public function xpathToXml($xpathQuery, $value)
		{



		}


		public function getValue($xpathQuery)
		{
			$nodes = $this -> xpath -> query($xpathQuery);

			if ($nodes -> length == 1)
			{
				return $nodes -> item(0) -> nodeValue;
			}

			if ($nodes -> length > 1)
			{
				$retour = array();
				for($i=0 ; $i < $nodes -> length ; $i++)
				{
					$retour[] = $nodes -> item($i) -> nodeValue;
				}
				return $retour;
			}

			return null;
		}


		public function transform($xslFile)
		{
			$docXslt = new DOMDocument();
			$docXslt -> load($xslFile);
			$this -> xsl -> importStyleSheet($docXslt);
			$this -> xml = $this -> xsl -> transformToXML($docXslt);
		}

	}

?>
