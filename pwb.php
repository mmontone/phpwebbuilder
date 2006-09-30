<?

/**
 * Includes all files and makes initialization for the application.
 * Controller Version.
 * */

//ob_start("ob_gzhandler");
ini_set('memory_limit', '-1');
set_time_limit(0);
ini_set('display_errors', true);

define('CHILD_SEPARATOR', ':');

// Configure the error reporting level in config.php
// Example:
// error_reporting=E_ERROR | E_WARNING | E_PARSE
// ;error_reporting=E_ERROR | E_PARSE
// ;error_reporting=E_ALL

if (defined('error_reporting')) {
	error_reporting(constant('error_reporting'));
}

require_once dirname(__FILE__) . "/lib/basiclib.php";
includeAll();
?>