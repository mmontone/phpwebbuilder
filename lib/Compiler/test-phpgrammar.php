<?php

require_once 'PHPCC.class.php';
require_once 'PHPGrammar.class.php';
require_once dirname(dirname(dirname(__FILE__))).'/Core/FunctionObject.class.php';
define('pwbdir',dirname(dirname(dirname(__FILE__))));
define('basedir',dirname(dirname(dirname(__FILE__))));
define('app_class','Test-grammar');
$_SESSION=array('shutdown_functions'=>array());
require_once dirname(dirname(dirname(__FILE__))).'/lib/basiclib.php';

error_reporting(E_ALL);
ini_set('memory_limit', '32M');

/*
preg_match('/\/\*.*?\*\//', '/* asdfasdf ', $matches);



var_dump($matches);exit;
*/
/* We first define the grammar*/
ob_start();
$g =& PHPGrammar::Grammar();
ob_end_clean();
htmlshow($g->print_tree());
$tl = 0;

foreach(getfilesrec(lambda('$file', '$v=substr($file, -4)==".php";return $v;'),dirname(__FILE__)) as $input){
	echo '<br/>parsing ';htmlshow($input);
	ob_start();

	set_time_limit($tl+=60);
	$comp = $g->compile(file_get_contents($input));
	if ($g->isError()){
		$ob = ob_get_contents();
		ob_end_clean();
		htmlshow($comp);
		echo $ob;
		exit;
	} else {
		ob_end_clean();
	}
	var_dump($comp);
}
?>