<?

/**
 * Handles session information, and logs in as "guest" 
 * if there is no one logged in. 
 */

session_start();
if(!isset($_SESSION["install"]))
	if (!isset($_SESSION[sitename])) {
		User::login("guest", "guest");
	} 
?>