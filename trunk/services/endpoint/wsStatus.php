<?

/**
 * @version		0.1 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	class WSStatus
	{
		// Objet commun pour le retour du service
		public function __construct($success, $message, $level = 0, $errorCode = null)
		{
			$this -> success = $success;
			$this -> message = (is_array($message)) ? implode('<br />', $message) : $message;
			//$this -> level = intval($level);
			$this -> errorCode = $errorCode;
		}
	
	}

?>