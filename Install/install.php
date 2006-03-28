<?php
session_start();	
	$_SESSION["install"]=true;
	$datas = array(
		"serverhost"=>"Database Server"
		,"basename"=>"Database Name" 
		,"baseuser"=>"Database Username"
		,"basepass"=>"Database Password"
		,"basedir"=>"Directorio de la aplicaci&oacute;n"
		,"appdir"=>"Directorio de las clases"
		,"app_class"=>"Application Class"	
		,"site_url"=>"Url de la aplicaci&oacute;n"
		,"DBObject"=>"Objeto de Base de Datos"
		,"baseprefix"=>"Prefijo de las Tablas"
		,"sitename"=>"Nombre de la aplicacion"
		,"pwbdir"=>"Directorio del PHPWebBuilder"
		,"icons_url"=>"Url de los íconos"
		,"peardir"=>"Directorio del PEAR"
	);
	$formm=$_REQUEST;
if(!isset($formm["serverhost"])){
	session_start();
	$configfile = dirname(dirname(dirname($_SERVER["DOCUMENT_ROOT"].$_SERVER["PHP_SELF"])))."/config.php";
	if ($_REQUEST["DEBUG"]=="YES"){
			ini_set('display_errors',true);
			error_reporting(E_ALL | E_STRICT);
		} else {
			error_reporting(0);
			ini_set('display_errors',FALSE);
		}
	
	$_SESSION[sitename]=array();
	$_SESSION[sitename]["Username"] = "guest";
	if(file_exists($configfile)){
		echo "Loading existing configuration";
		require_once dirname(__FILE__)."/ConfigReader.class.php";
		$conf = new ConfigReader;
		$act = $conf->read($configfile);
		foreach($datas as $data=>$name){
			$default[$data] = $act[$data];	
		}
	} else {
		echo "Creating new configuration, $configfile doesn't exist";
		$default["nivelcount"] = "0";
		$default["serverhost"]="localhost";
		$default["AdminPass"]="lihuen";
		$default["TipoGrafico"] = "barras";
		$default["basedir"]=dirname(dirname($_SERVER["DOCUMENT_ROOT"]. dirname($_SERVER["PHP_SELF"])));	
		$default["appdir"]=$default["basedir"]."/MyInstances";				
		$default["site_url"]="http://".dirname(dirname($_SERVER["HTTP_HOST"].dirname($_SERVER["PHP_SELF"])));
		$default["DBObject"]="MySQLdb";
		$default["baseprefix"]="";
		$default["pwbdir"]=dirname($_SERVER["DOCUMENT_ROOT"]. dirname($_SERVER["PHP_SELF"]));
		$default["icons_url"]="http://".$_SERVER["HTTP_HOST"].dirname(dirname($_SERVER["PHP_SELF"]));
		$default["peardir"]="";
	}

	$formdata ="<table>";
	foreach($datas as $data=>$name){
		$formdata .= "<tr><td>$name: </td><td><input type=\"text\" name=\"$data\" size=\"60\" value=\"".$default[$data]."\"/></td></tr>";
	}
	$formdata .="</table>";
	$formdata .= "Eliminar lo anterior:<input type=\"checkbox\" name=\"execEliminar\"/><br/>";	
	$formdata .= "<input type=\"hidden\" name=\"DEBUG\" value=\"".$_REQUEST["DEBUG"]."\"/><br/>";
	$form =	"<form action=\"install.php".
			"\" method=\"POST\" name=\"Install\">" . $formdata.
			"<input name=\"execInstall\" type=\"submit\" /></form>";
	echo $form;
} else { 
	$configfile = $formm["basedir"]."/config.php";
	foreach($datas as $data=>$name){
		$form[$data] =$formm[$data];
	}
	require_once $formm["basedir"]."/Configuration/ConfigReader.class.php";
	$_SESSION[$form["sitename"]]=$_SESSION[sitename];
	$conf = new ConfigReader;
	$conf->write($configfile, $form);	
	require_once $formm["basedir"]."/Configuration/pwbapp.php";
    trigger_error("Importado el archivo de configuración");
	// conseguir las tablas a crear
if($formm["execEliminar"]=="on"){
	$sql="";
	foreach(get_subclasses('PersistentObject') as $c) {
		if (strcasecmp($c,"ObjSQL")==0) continue;
		$c = new $c;
		$sql .= "DROP TABLE IF EXISTS `".$c->tableName() ."`;";
	}
	$sqls = split(";",$sql);
	echo "<BR/>";
	echo ereg_replace(";", ";<BR/>", $sql);
	$db = new MySQLdb;
	$db->batchExec($sqls);
}
  	$dbc = new DBController();
  	$sql = $dbc->modsNeeded();
	// crear las tablas
	require_once dirname(__FILE__)."/SQLs/minimal-data.php";
	$sql .= $basesql;
	$sqls = split(";",$sql);
	echo "<BR/>";
	echo ereg_replace(";", ";<BR/>", $sql);
	$db = new MySQLdb;
	$db->batchExec($sqls);
	
	 
	echo "<br/>El sitio se ha configurado existosamente";
	session_destroy();
}
?>