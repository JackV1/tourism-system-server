<?php

/**
 * @version		0.3 alpha-test - 2013-01-25
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class maintenanceDb
	{
	
		const SQL_ARCHIVE_SESSIONS = "INSERT INTO sitSessionsArchive SELECT * FROM sitSessions s WHERE s.sessionEnd < '%s'";
		const SQL_PURGE_SESSIONS = "DELETE FROM sitSessions WHERE sessionEnd < '%s'";
		

		public static function purgeSessions()
		{
			$now = date('Y-m-d H:i:s');
			tsDatabase::query(self::SQL_ARCHIVE_SESSIONS, array($now));
			tsDatabase::query(self::SQL_PURGE_SESSIONS, array($now));
		}
		
		
	}
	
	
?>