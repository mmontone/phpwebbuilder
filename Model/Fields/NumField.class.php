<?php

class NumField extends DataField {
    var $range = null;
	function createInstance($params){
		parent::createInstance($params);
		$this->range = @$params['range'];
		$this->value=0;
		$this->buffered_value=0;
	}
	function increment(){
		$this->setValue($this->getValue()+1);
	}
	function decrement(){
		$this->setValue($this->getValue()-1);
	}
	function defaultValues($params){
		return array_merge(array('range'=>null, 'numtype'=>'int'), parent::defaultValues($params));
	}
	function isRanged() {
		return $this->range != null;
	}

	function getRange() {
		return $this->range;
	}

    function &visit(&$obj) {
        return $obj->visitedNumField($this);
    }
    function getValue() {
        return str_replace(",",".",parent::getValue());
    }
	function SQLvalue() {
		return "'" . $this->getValue() . "'" . ", ";
	}

    function &validate() {
        return $this->validate_ereg("[0-9]+(\.[0-9]*)?",$this->displayString . ' is not a number');
    }
}
?>