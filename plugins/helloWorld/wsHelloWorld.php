<?php
	
	final class wsHelloWorld extends wsEndpoint
	{
		
		protected static function _sayHi()
		{
			return array('msg' => 'La serveur te dit bonjour !');
		}
		
	}
	
?>