<?php

require_once dirname(__FILE__) . '/ValueModel.class.php';

class PluggableAdaptor extends ValueModel
{
	var $get_function;
	var $set_function;

    function PluggableAdaptor(&$get_function, &$set_function) {
    	parent::ValueModel();

    	$this->get_function =& $get_function;
    	$this->set_function =& $set_function;
    }

    function primitiveSetValue(&$value) {
    	$this->set_function->setValue($value);
    }

    function &getValue() {
    	return $this->get_function->getValue();
    }
}
?>