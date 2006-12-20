<?php

$d = $_REQUEST['dir'];
$n = strlen($d)+1;

if ($d===null){
	echo "parameter dir should be a directory";
}

function includefile(&$file) {
	global $n;
	if (is_dir($file)) {
			$gestor=opendir($file);
			while (false !== ($f = readdir($gestor))) {
				if (substr($f,-1)!='.')
					includefile(implode(array($file,'/',$f)));
			}
	} else {
		if (substr($file, -4)=='.php') {
                  //echo "Including file: " . $file;
                  echo "<br/>compile_once (\dirname(__FILE__).'/".substr($file, $n)."');";
		}
	}
}
echo '$d = dirname(__FILE__);';
includefile($d);

?>