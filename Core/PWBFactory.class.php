<?php

class PWBFactory extends PWBObject{
	function &createFor(&$target) {
		$c = getClass($target);
		$base = getClass($this);
		$class =& $GLOBALS['PWBFactory'][$c.$base];
		if ($class===null){
			$class = PWBFactory::findClass($c, $base);
		}
		$v =& new $class;
		return $v->createInstanceFor($target);
	}
	function findClass($c, $base){
		$ok = false;
		while(!$ok) {
			$class = $c.$base;
			$ok = Compiler::requiredClass($class);
			$c = get_parent_class($c);
		}
		return $class;
	}
}

?>