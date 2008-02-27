<?php
class VersionField extends NumField {
	function createInstance($params) {
		parent :: createInstance($params);
		$this->value = -1;
		$this->buffered_value = -1;
	}

	function & visit(& $obj) {
		return $obj->visitedNumField($this);
	}

	function setValue($value) {
		// Don't register a modification
		$this->buffered_value = $value;
	}
	function flushChanges() {

	}

	function commitChanges() {

	}

	function shouldLoadFrom($reg) {
		$val = @ $reg[$this->sqlName()];
		return $this->getValue() < $val;
	}
}
?>