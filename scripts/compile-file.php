<?php
ini_set('error_reporting', 'E_ERROR');
$app_dir = $_REQUEST['app_dir'];
$file = $_REQUEST['file'];
$verbose = $_REQUEST['verbose'] == 'yes';


if (!file_exists($file)) {
	echo 'The file `' . $file . '` does not exist';
	return 1;
}

if (!file_exists($app_dir)) {
	echo 'The application `' . $app_dir . '` does not exist';
	return 1;
}

require_once $app_dir . 'Configuration/ConfigReader.class.php';
$config_reader = & new ConfigReader();
$config_reader->load($app_dir . 'config.php');

$pwb_dir = constant('pwbdir');
//$pwb_dir = '../';


require_once $pwb_dir . 'lib/basiclib.php';
$compiler =& Compiler::Instance();

$tmpname = $compiler->getTempFile($file, $compiler->toCompileSuffix);

echo 'Compiling '.$file .' to '.$tmpname  . '...';
$fo = fopen($tmpname, 'w');
$f = '<?php '.$compiler->compileFile($file).' ?>';


if (!fwrite($fo, $f)) {
	echo "\nError: could not write\n" . $tmpname;
}
else {
	echo "done\n";
	if ($verbose) {
	  echo 'Compiler directives: ' . constant('compile') . "\n";
	    echo "Output:\n"  . $f . "\n";
	}
}

fclose($fo);

?>