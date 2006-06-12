<?

/**
 * Includes all files and makes initialization for the application.
 * Controller Version.
 * */
ob_start("ob_gzhandler");
ini_set('memory_limit', '-1');
set_time_limit(0);
ini_set('display_errors', true);

error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(E_ALL);

require_once dirname(__FILE__) . "/lib/basiclib.php";



if (!defined('modules')) {
	define('modules', "BaseClasses,Application,Model,Instances,XML,View,database,NewTemplates,DefaultCMS");
	define('app_class', "DefaultCMSApplication");
}

$modules = split(",", modules);

/*$config = array (
	basedir . "/MyConfig"
);

foreach ($config as $dir) {
	includemodule($dir);
}*/

foreach ($modules as $dir) {
	includemodule(pwbdir.'/'.trim($dir));
}

define('app', "MyInstances,MyComponents" );
$app = split(",", app);

foreach ($app as $dir) {
	includemodule(basedir."/".$dir);
}

session_start();
if(!isset($_SESSION["install"]))
	if (!isset($_SESSION[sitename])) {
		User::login("guest", "guest");
	}
?>