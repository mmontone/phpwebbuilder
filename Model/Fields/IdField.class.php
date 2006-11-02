<?php

class IdField extends NumField {
    function &visit(&$obj) {
        return $obj->visitedIdField($this);
    }
    function fieldName ($operation) {
        if ($operation == 'SELECT') {
            return parent::fieldName ($operation);
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
    	$this->buffered_value =& $value;
    }

    function validate() {
    	return false;
    }

	function flushChanges() {

	}

	function commitChanges() {

	}
}

?>