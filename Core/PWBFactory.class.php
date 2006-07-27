<?php

class PWBFactory {
	function &createFor(&$target) {
		$ok = false;
		$c = getClass($target);
		while(!$ok) {
			$name = $c.getClass($this);
			$ok = class_exists($name);
			$c = get_parent_class($c);
		}
		$v=& new $name;
		return $v->createInstanceFor($target);
	}
}

?>