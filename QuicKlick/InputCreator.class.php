<?php
class InputGenerator extends PWBFactory{
	function createInstanceFor(&$widget){
		return "";
	}
}

class InputInputGenerator extends InputGenerator{
	function createInstanceFor(&$widget){
		$pos = array('lala', '', '123');
		return $pos[array_rand($pos)];
	}
}
?>