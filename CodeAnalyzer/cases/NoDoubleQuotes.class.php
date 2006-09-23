<?php

class NoDoubleQuotes extends AnalisysCase {
	function analyze($file) {
		$src = file_get_contents($file);
		$out = 'Found double quotes in: ' . $file ."\n";
		//$out .= token_get_all($src);
		return $out;
    }

    function printString()  {
    	return 'No double quotes';
    }

    function description() {
    	return 'Checks uses of double quotes. These are inefficient. Simple quotes are recommended';
    }
}
?>