<?php

$d = $_REQUEST['dir'];
$n = strlen($d)+1;

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
                  echo "require_once '".substr($file, $n)."';<br/>";
		}
	}
}

includefile($d);

?>