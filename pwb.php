<?

/**
 * Includes all files and makes initialization for the application.
 * Controller Version.
 * */

ini_set('memory_limit', '-1');
set_time_limit(0);
ini_set('display_errors', true);

error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(E_ALL);

require_once dirname(__FILE__) . "/basiclib.php";



if (!defined('modules')) {
	define('modules', "Model,Instances,newcontroller,database,NewTemplates,DefaultCMS");
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

$app = array (
	basedir . "/MyInstances"
		//,basedir."/MyControllers"

);
foreach ($app as $dir) {
	includemodule($dir);
}
require_once "session.php";
?>