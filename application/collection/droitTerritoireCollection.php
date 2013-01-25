<?php

/**
 * @version		0.3 alpha-test - 2013-01-25
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class droitTerritoireCollection extends baseCollection
	{
		
		public function offsetSet($offset, $oDroitTerritoire)
		{
			parent::offsetSet($offset, $oDroitTerritoire);
		}
		
	}

?>