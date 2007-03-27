<?php

class PluggableAdaptor extends PWBObject {#@use_mixin ValueModel@#
	var $get_function;
	var $set_function;

    function PluggableAdaptor(&$get_function, &$set_function) {
    	parent::PWBObject();

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