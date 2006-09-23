<?php
class NoCopies extends AnalisysCase {

	function analyze($file) {
		$src = file_get_contents($file);
		$out = 'Found copies in: ' . $file . "\n";
		//$out .= token_get_all($src);
		return $out;
	}

	function printString() {
		return 'No implicit copies';
	}

	function description() {
		return 'Checks possible copies passing';
	}
}
?>