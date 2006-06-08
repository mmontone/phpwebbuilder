<?php

 function appendSound($path) {
	if (is_dir($path)) {
		$reader = opendir($path);
		while (false !== ($file = readdir($reader))) {
			if (!ereg($file, "\.$"))
				appendSound($path .	'/' . $file);
		}
	}
	else {
		if (isSound($path)) {
 			echo "&lt;embed src=\"" . $path."\" hidden=true autostart=false&gt;</br>";
		}
	}
}

function isSound($file) {
	return ereg('(.*wav$)|(.*mp3$)',$file);
}

if (!$_REQUEST['path']) {
	echo 'Enter a path in "path"';
	exit;
}

appendSound($_REQUEST['path']);

?>
