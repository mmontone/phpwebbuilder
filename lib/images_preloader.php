<?php
function appendImage($path, &$images) {
	if (is_dir($path)) {
		$reader = opendir($path);
		while (false !== ($file = readdir($reader))) {
			if (!ereg($file, "\.$"))
				appendImage($path .	'/' . $file, $images);
		}
	}
	else {
		if (isImage($path)) {
			//echo "Appending $path </br>";
			$images[] = "\"$path\"";
		}
	}
}

function isImage($file) {
	return ereg('(.*gif$)|(.*jpg$)',$file);
}

if (!$_REQUEST['path']) {
	echo 'Enter a path in "path"';
	exit;
}


$images = array();
appendImage($_REQUEST['path'], $images);
$images = implode(',</br>', $images);

echo "var images = new Array($images);";

?>
