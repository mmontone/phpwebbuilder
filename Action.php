<?

require_once dirname(dirname($_SERVER["DOCUMENT_ROOT"].$_SERVER["PHP_SELF"]))."/Configuration/pwbapp.php";

trace_params();

/*Si no especifica un controlador, se carga el default*/
if (!(isset($_REQUEST["Controller"])))
	$_REQUEST["Controller"] = "ShowController";

$controller = new $_REQUEST["Controller"];
$action = 'begin';
if ($_REQUEST['action'])
  $action = $_REQUEST['action'];

$controller->initialize($params);
echo $controller->execute($action, $_REQUEST);

?>