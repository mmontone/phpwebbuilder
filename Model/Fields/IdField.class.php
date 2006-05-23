<?php

require_once dirname(__FILE__) . '/NumField.class.php';

class IdField extends NumField {
    function &visit(&$obj) {
        return $obj->visitedIdField($this);
    }
    function IdField ($name, $isIndex) {
        parent::DataField($name, $isIndex);
    }
    function fieldName ($operation) {
        if ($operation == 'SELECT') {
            return parent::fieldName ($operation);
        }
    }
    function insertValue() {
    	return "";
    }
    function setID($id) {
        $this->setValue($id);
    }
    function check($val) {
    	return true;
    }
}
?>