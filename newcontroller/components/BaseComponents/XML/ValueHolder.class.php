<?php

require_once dirname(__FILE__) . '/ValueModel.class.php';

class ValueHolder extends ValueModel
{
    // Private!!
    var $__value;

    function ValueHolder(&$value) {
    	parent::ValueModel();
    	$this->primitiveSetValue($value);
    }

    function &getValue() {
    	return $this->__value;
    }

    function primitiveSetValue(&$value) {
    	$this->__value =& $value;
    }
}
?>