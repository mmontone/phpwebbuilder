<?php

require_once dirname(__FILE__) . '/NumField.class.php';

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
        // Don't register a modification, so we don't call setValue
        $this->value = $id;
        $this->buffered_value = null;
    }
	function commitChanges(){}
    function check($val) {
    	return true;
    }
}
?>