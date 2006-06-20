<?php

class FieldPresenterFactory {
	function &createFor(&$field){
		$ok = false;
		$c = getClass($field);
		while(!$ok) {
			$name = $c.getClass($this);
			$ok = class_exists($name);
			$c = get_parent_class($c);
		}
		$v=& new $name;
		return $v->componentForField($field);
	}
}
?>