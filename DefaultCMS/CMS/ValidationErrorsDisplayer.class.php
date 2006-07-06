<?php

class ValidationErrorsDisplayer extends Component {
	var  $errors;

    function ValidationErrorsDisplayer(&$errors) {
    	$this->errors =& $errors;
    	parent::Component();
    }

    function initialize() {
		//print_backtrace();
		foreach(array_keys($this->errors) as $i) {
    		$error =& $this->errors[$i];
    		$error->accept($this);
    	}
    }

    function visitOneOfException(&$ex) {
    	$this->addComponent(new Label($ex->getMessage()));
    }

    function visitEmptyFieldException(&$ex) {
    	$this->addComponent(new Label($ex->getMessage()));
    }

    function visitValidationException(&$ex) {
    	$this->addComponent(new Label($ex->getMessage()));
    }
}

?>