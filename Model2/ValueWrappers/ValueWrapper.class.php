<?php

class ValueWrapper {
	var $value;

    function ValueWrapper($value) {
    	$this->value =& $value;
    }

    function getValue() {
    	return $this->value;
    }

    function setValue($value) {
    	$this->value =& $value;
    }
}
?>