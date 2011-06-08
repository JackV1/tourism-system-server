<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/collection/thesaurusCollection.php');

	final class thesaurusModele extends baseModele implements WSDLable
	{
		
		protected $libelle;
		protected $codeThesaurus;
		protected $prefixe;
		protected $idThesaurus;
		
		
		public function __toString()
		{
			$str = '<h2>Thésaurus</h2>';
			$str .= '<h4>Code thesaurus : ' . $this -> codeThesaurus . '</h4>';
			$str .= '<h4>Libelle : ' . $this -> libelle . '</h4>';
			$str .= '<h4>Prefixe : ' . $this -> prefixe . '</h4>';
			$str .= '<h4>idThesaurus : ' . $this -> idThesaurus . '</h4>';
			return $str;
		}
		
	}



?>