<?php

class OrValidation extends Validation {
	var $validations;
	var $error_msg;

    function OrValidation(&$validations, &$error_msg) {
    	$this->validations =& $validations;
    	$this->error_msg =& $error_msg;
    }

    function validate(&$error_handler) {
    	$eh =& new OrValidationErrorHandler();
    	$eh->validate($this, $error_handler);
    }

    function errorMessage() {
    	return $this->errorMessage->callWith($this);
    }
}

class OrValidationErrorHandler
{
	// No tengo short-circuit porque no tengo continuations...no gusto
	// Me parece que voy a hacer la validacion de antes y listo
	var $success;

	function validate(&$or_validation, &$error_handler) {
		$this->success = count($or_validation->validations);
		foreach ($or_validation->validations as $validation) {
			$validation->validate(new FunctionObject($this,'validationFailed'));
		}

		if (!$this->success) {
			$e =& new OrValidationPWBException($or_validation->errorMessage());
			$e->raise($error_handler);
		}
	}

	function validationFailed(&$exception) {
		$this->success--;
	}
}

class OrValidationPWBException extends PWBException {}

class NonEmptyOrValidationErrorMsg extends FunctionObject {
	function callWith(&$or_validation) {
		$validated = array();
		foreach ($or_validation->validations as $validation) {
			$validated[] = $validation->value_holder->displayString;
		}
		$validated = implode(',', $validated);
		return 'Please fill in one of ' . $validated;
	}
}
?>