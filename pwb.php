<?
/**
 * Includes all files and makes initialization for the application.
 * Controller Version.
 * */
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
            	 "templates,ajax"
      );

define('app_class',"DefaultCMSApplication");

$modules = split(",",modules);

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