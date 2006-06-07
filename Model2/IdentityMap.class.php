<?php

class IdentityMap {
	var $maps;

	function IdentityMap() {
		$this->maps = array ();
	}

	function & objectWithId($class, $id) {
		if (!($mapper = & $this->maps[$class]))
			$this->maps[$class] = & new ClassIdentityMap($class);
		return $mapper->getObjectWithId($id);
	}
}

class ClassIdentityMap {
	var $class;
	var $size;
	var $map;

	function ClassIdentityMap($class, $size = 10) {
		$this-> class = $class;
		$this->size = $size;
		$this->map = array ();
	}

	function & objectWithId($id) {
		if ($this->map[$id] == null) {
			$obj = & new $this-> class;
			$obj->load();
			$this->map[$id] = & obj;
		}

		return $this->map[$id];
	}
}

class IdentityMapScheduling {}

class FIFOIdentityMapScheduling {}

?>