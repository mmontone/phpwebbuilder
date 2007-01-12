<?php
class SuperField extends NumField {
	function & visit(& $obj) {
		return $obj->visitedSuperField($this);
	}
	function updateString() {}

	function setValue($value) {
		// Don't register modifications
    	$this->buffered_value =& $value;
    }

    function &validate() {
    	$f = false;
    	return $f;
    }

    function flushChanges() {

	}

	function commitChanges() {

	}
}
?>