<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class ficheFichierCollection extends baseCollection
	{
		
		public function offsetSet($offset, $oFicheFichier)
		{
			parent::offsetSet($offset, $oFicheFichier);
		}
		
	}

?>