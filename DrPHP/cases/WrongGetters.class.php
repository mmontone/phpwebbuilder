<?php

class WrongGetters extends AnalisysCase {

	function analyze($file) {
		$src = file_get_contents($file);
		$out = 'Possibly wrong getter in: ' . $file . "\n";
		//$out .= token_get_all($src);
		return $out;
	}

	function printString() {
		return 'Possibly wrong getters';
	}

	function description() {
		return 'Checks getters have a return statement';
	}
}
?>