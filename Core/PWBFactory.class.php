<?php

class PWBFactory extends PWBObject{
	function &createFor(&$target) {
		$ok = false;
		$c = getClass($target);
		$base = getClass($this);
		while(!$ok) {
			$name = $c.$base;
			$ok = Compiler::requiredClass($name);
			$c = get_parent_class($c);
		}
		//echo 'Creating ' . $name  . ' for ' . getClass($target) . '<br />';
		$v =& new $name;
		return $v->createInstanceFor($target);
	}
}

?>