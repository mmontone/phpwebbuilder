<?
/**
 * Includes all files and makes initialization for the application.
 * Controller Version.
 * */
xdebug_start_profiling();
ini_set('display_errors',true);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(E_ALL);

require_once dirname(__FILE__) . "/basiclib.php";

//include_once($_SERVER["DOCUMENT_ROOT"].dirname(dirname($_SERVER['PHP_SELF']))."/Configuration/pwbapp.php");

$config = array(
	basedir."/MyConfig"
);
$app = array(
   basedir."/MyInstances",
   basedir."/MyControllers"
);

define('modules',"Model,OldViews,Controllers,deprecated," .
				 "Instances,database,".
            	 "templates,ajax,NewTemplates, DefaultCMS," .
            	 "flowview, Application"
      );

define('app_class',"DefaultCMSApplication");

$modules = explode(",",modules);

foreach ($config as $dir) {
    includefile($dir);
}

foreach ($modules as $dir) {
    includemodule(trim($dir));
}
foreach ($app as $dir) {
    includefile($dir);
}
xdebug_dump_function_profile(5);
//require_once "session.php";


?>