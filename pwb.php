<?
/**
 * Includes all files and makes initialization for the application.
 * Controller Version.
 * */
ini_set('memory_limit', '8M');
ini_set('display_errors',true);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(E_ALL);

require_once dirname(__FILE__) . "/basiclib.php";

$config = array(
	basedir."/MyConfig"
);
$app = array(
   basedir."/MyInstances"
   //,basedir."/MyControllers"
);
if (!defined('modules')) {
	define('modules',"Model,Instances,newcontroller,database,NewTemplates,DefaultCMS");
	define('app_class',"DefaultCMSApplication");
}
$modules = split(",",modules);

foreach ($config as $dir) {
    includefile($dir);
}
//echo "<br/>for loading the configuration we used: " .memory_get_usage();
$prev = memory_get_usage();

foreach ($modules as $dir) {
    includemodule(trim($dir));
  //  echo "<br/>for $dir we used: " .(memory_get_usage() -$prev) ;
    $prev = memory_get_usage();
}
foreach ($app as $dir) {
    //echo "<br/>for $dir we used: " .(memory_get_usage() -$prev) ;
    $prev = memory_get_usage();
    includefile($dir);
}
//echo "<br/>in total for loading the application we used: " .memory_get_usage() ;
require_once "session.php";
?>