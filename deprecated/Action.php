<?
define('modules',
	"Model,Instances,database,Controllers,OldViews");
define('app',"MyInstances,MyControllers");

require_once dirname(dirname($_SERVER["DOCUMENT_ROOT"].$_SERVER["PHP_SELF"]))."/Configuration/pwbapp.php";

includefile($f = basedir.'/MyControllers');

trace_params();

/*Si no especifica un controlador, se carga el default*/
/*if (!(isset($_REQUEST["Controller"])))
	$_REQUEST["Controller"] = "ShowController";*/

$controller = new $_REQUEST["Controller"];

if (isset($_REQUEST['action'])) {
  $action = $_REQUEST['action'];
} else {
	$action = 'begin';
}
$controller->initialize($params);
echo $controller->execute($action, $_REQUEST);
//xdebug_dump_function_trace(4);
?>