<?php

require_once '../lib/basiclib.php';

$args = $_SERVER['argv'];

$help = "PWB Multiple Dispatch Compiler\n\n" .
		"Syntax: mdc [OPTIONS] <files>\n" .
        "OPTIONS:\n" .
        "-d <dir> : The output directory for compiled files\n\n";

$gethelp = "Type \"mdc -h\" for help.\n";

if (count($args) == 1) {
	echo "Not enough arguments. " . $gethelp;;
	exit;
}


$i = 1;

$dir = '.';

while (ereg('-(.)+|--(.)+', $args[$i])) {
	// It's an option
	switch ($args[$i]) {
		case '-h' : echo $help;
					exit;
		case '-d' : $dir = $args[$i + 1];
					$i ++;
					break;
		default: echo 'Invalid option ' . $args[$i] . '. ' . $gethelp;
				 exit;
	}
	$i++;
}

if ($args[$i] == null) {
	echo 'Files missing. ' . $gethelp;
	exit;
}

$files = array();

for ($k = $i; $k < count($args); $k++) {
	$files[] = $args[$k];
}

compile_md_files($files, $dir);

function compile_md_files($files, $dir) {
	foreach ($files as $file) {
		compile_md_file($file, $dir);
	}
}

function compile_md_file($file, $output_dir) {
	echo "Compiling $file ...";
	$compiled_src = compile_md_src(Spyc::YAMLLoad($file));
	$compiled_src = '<?php ' . $compiled_src . '?>';
	//echo "\n\n$compiled_src\n\n";
	$basefile = basename($file);
	$output_file = $output_dir . '/' . substr($basefile, 0, strlen($basefile) - 3) . '.php';
	$f = fopen($output_file, 'w');
	if (!$f) {
		echo "Compilation failed\n";
		exit;
	}
	fwrite($f, $compiled_src);
	fclose($f);
	echo "OK\n";
}

?>