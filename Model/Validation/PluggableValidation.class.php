<?php

class PluggableValidation extends Validation {

    var $validation_function;

    function PluggableValidation(&$value_model, &$validation_function) {
    	parent::FunctionValidation($value_model);
    	$this->validation_function =& $validation_function;
    }

    function validate(&$error_handler) {
    	$this->validation_function->executeWith(array(&$this->value_model, &$error_handler));
    }
}
?>