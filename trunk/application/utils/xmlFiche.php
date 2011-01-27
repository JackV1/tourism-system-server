<?php
	
/**
 * @version		0.1 alpha-test - 2011-01-27
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
		
		public function __construct($xml)
		{
			$this -> xml = $xml;
			$this -> dom = new DOMDocument("1.0");
			$this -> dom -> loadXml($this -> xml);
			
			$this -> xpath = new DOMXPath($this -> dom);
			$this -> xsltProc = new XSLTProcessor();
		}
		
		
		public function getXml()
		{
			return $this -> xml;
		}
		
		
		public function xpathToXml($xpathQuery, $value)
		{
			
			
			
		}
		
		
		public function getValue($xpathQuery)
		{
			$nodes = $this -> xpath -> query($xpathQuery);
			if (count($nodes) == 1)
			{
				return $nodes[0] -> nodeValue;
			}
			
			$retour = array();
			foreach($nodes as $node)
			{
				$retour[] = $node -> nodeValue;				
			}
			
			return $retour;
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