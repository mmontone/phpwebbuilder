<?php
require_once dirname(__FILE__) . '/NumField.class.php';
class SuperField extends NumField {
	function & visit(& $obj) {
		return $obj->visitedSuperField($this);
	}
	function updateString() {}

	function check() {
		return TRUE;
	}

    function setValue($value) {
		// Don't register modifications
    	$this->buffered_value =& $value;
    }
}
?>