<?php

class PWBFactory {
	function &createFor(&$target) {
		$ok = false;
		$c = getClass($target);
		$base = getClass($this);
		while(!$ok) {
			$name = $c.$base;
			$ok = class_exists($name);
			$c = get_parent_class($c);
		}
		$v =& new $name;
		return $v->createInstanceFor($target);
	}
}

?>