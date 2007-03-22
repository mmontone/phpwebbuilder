<?php

class IdField extends NumField {
    function &visit(&$obj) {
        return $obj->visitedIdField($this);
    }
    function fieldNamePrefixed ($operation, $pfx) {
        if ($operation == 'SELECT') {
            return parent::fieldNamePrefixed ($operation, $pfx);
        }
    }

	function updateString() {
		return '';
	}
    function insertValue() {
    	return '';
    }

    function setID($id) {
        $this->setValue($id);
    }

    function setValue($value) {
		// Don't register a modification
    	$this->buffered_value = $value;
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