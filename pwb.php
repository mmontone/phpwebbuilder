<?

/**
 * Includes all files and makes initialization for the application.
 * Controller Version.
 * */

ob_start("ob_gzhandler");
ini_set('memory_limit', '-1');
set_time_limit(0);
ini_set('display_errors', true);

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ERROR | E_PARSE);
//error_reporting(E_ALL);

require_once dirname(__FILE__) . "/lib/basiclib.php";



if (!defined('modules')) {
	define('modules', "Core,Application,Model,Instances,View,database,DefaultCMS");
}
if (!defined('app_class')) {
	define('app_class', "DefaultCMSApplication");
}

$modules = explode(",", modules);

foreach ($modules as $dir) {
	includemodule(pwbdir.'/'.trim($dir));
}

define('app', "MyInstances,MyComponents" );
$app = explode(",", app);

foreach ($app as $dir) {
	includemodule(basedir."/".$dir);
}

/*Session handling*/
session_name(strtolower(app_class));
if ($_REQUEST["restart"]=="yes") {
  $sessionid = $_COOKIE[session_name()];
  $orgpath = getcwd();
  chdir(PHP_BINDIR);
  chdir(session_save_path());
  $path = realpath(getcwd()).'/';
  if(file_exists($path.'sess_'.$sessionid)) {
   unlink($path.'sess_'.$sessionid);
  }
  chdir($orgpath);
  session_regenerate_id();
}
session_start();
if ($_REQUEST["reset"]=="yes") {
	unset($_SESSION[sitename][app_class]);
}

?>