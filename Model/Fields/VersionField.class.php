<?php

class VersionField extends NumField {
    function &visit(&$obj) {
        return $obj->visitedNumField($this);
    }

    /*
    function setValue($value) {
		// Don't register a modification
    	$this->buffered_value =& $value;
    }
    */
}

?>