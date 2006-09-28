<?php

class ValueHolder extends ValueModel
{
    // Private!!
    var $__value;

    function ValueHolder($value) {
    	if (func_num_args() == 0)
    		print_backtrace();
    	parent::ValueModel();
    	$this->primitiveSetValue($value);
    }

    function getValue() {
    	return $this->__value;
    }

	function setValue($value) {
		$old_value =& $this->getValue();
		$this->primitiveSetValue($value);
		$params = array();
		$params['value'] =& $this->getValue();
		$params['old_value'] =& $old_value;
		$this->triggerEvent('changed', $params);
	}

    function primitiveSetValue($value) {
	   	$this->__value = $value;
    }
}

?>