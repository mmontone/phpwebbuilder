<?php

class IdField extends NumField {
    /*function defaultValues($params){
    	return array_merge(array('columnName'=>'id'),parent::defaultValues($params));
    }*/
    function &visit(&$obj) {
        return $obj->visitedIdField($this);
    }

	function updateString() {
		return '';
	}
    function insertValue() {
    	return ($this->value? $this->value:'NULL'). ", " ;
    }

    function setID($id) {
        $this->setValue($id);
    }

    function setValue($value) {
		// Don't register a modification
		$this->value = $value;
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