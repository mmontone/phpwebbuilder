<?php

class View  {
  var $controller;

	/**
	 * Creates the object View to handle this specific object.
	 * If there isn't an adecuate one, returns the one to handle
	 * it's parent.
	 */
	function &viewFactory(&$obj) {
		$ok = false;
		$c = get_class($obj);
		while(!$ok) {
			$name = $c.get_class($this);
			$ok = class_exists($name);
			$c = get_parent_class($c);
		}
		$v=& new $name;
		$v->obj =& $obj;
		return $v;
	}
	/*
	 * Creates a view for the parameter object.
	 * */
	function &viewFor(&$obj) {

	   if ($obj == null) debug_print_backtrace();
		return $obj->visit($this);
		//return $this->viewFactory($obj);
	}
}
?>