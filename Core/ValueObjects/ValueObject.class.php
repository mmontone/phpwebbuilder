<?php

class ValueObject {
	var $value;

    function ValueObject($value) {
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