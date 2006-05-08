<?php

class FieldComponentFactory {
	function &createFor(&$field){
		$ok = false;
		$c = get_class($field);
		while(!$ok) {
			$name = $c.get_class($this);
			$ok = class_exists($name);
			$c = get_parent_class($c);
		}
		$v=& new $name;
		return $v->componentForField($field);
	}
}
?>