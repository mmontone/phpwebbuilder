<?php

class VersionField extends NumField {
    function &visit(&$obj) {
        return $obj->visitedNumField($this);
    }

    function setValue($value) {
		// Don't register a modification
		$this->buffered_value =& $value;
    }
   function flushChanges() {

	}

	function commitChanges() {

	}

    function shouldLoadFrom($reg) {
    	$val = @$reg[$this->sqlName()];
        return ($this->getValue() == 0) or ($this->getValue() < $val);
    }
}

?>