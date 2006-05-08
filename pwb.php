<?

/**
 * Includes all files and makes initialization for the application.
 * Controller Version.
 * */

//ini_set('memory_limit', '5M');

ini_set('display_errors', true);

error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(E_ALL);

require_once dirname(__FILE__) . "/basiclib.php";

set_time_limit(120);

$config = array (
	basedir . "/MyConfig"
);
$app = array (
	basedir . "/MyInstances"
		//,basedir."/MyControllers"

);

if (!defined('modules')) {
	define('modules', "Model,Instances,newcontroller,database,NewTemplates,DefaultCMS");
	define('app_class', "DefaultCMSApplication");
}

$modules = split(",", modules);

foreach ($config as $dir) {
	includefile($dir);
}

foreach ($modules as $dir) {
	includemodule(trim($dir));
}
foreach ($app as $dir) {
	includefile($dir);
}
require_once "session.php";
?>