<?php

class Validation {
    var $value_model;

    function Validation(&$value_model) {
    	$this->value_model =& $value_model;
    }

    function validate(&$error_handler) {

    }

    function &validated() {
    	return $this->value_model;
    }

    function &getValue() {
    	return $this->value_model->getValue();
    }

    function triggerValidationError(&$error_handler, $msg) {
    	$error_handler->callWith($msg);
    }
}

?>