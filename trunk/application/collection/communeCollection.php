<?php
	
/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
	
	final class communeCollection extends baseCollection
	{
		
		public function offsetSet($offset, $oCommune)
		{
            parent::offsetSet($offset, $oCommune);
		}
		
	}

?>