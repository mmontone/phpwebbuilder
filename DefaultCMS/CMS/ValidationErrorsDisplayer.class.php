<?php

class ValidationErrorsDisplayer extends Component {
	var  $errors;

    function ValidationErrorsDisplayer($errors) {
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
    	$this->addComponent(new ErrorMessage(array('error_message' => $ex->getMessage())));
    }

    function visitEmptyFieldException(&$ex) {
    	$this->addComponent(new ErrorMessage(array('error_message' => $ex->getMessage())));
    }

    function visitValidationException(&$ex) {
    	$this->addComponent(new ErrorMessage(array('error_message' => $ex->getMessage())));
    }

    function accept() {
		$this->callback('on_accept');
	}

	function onAccept(&$function) {
		$this->registerCallback('on_accept', $function);
		$this->addComponent(new CommandLink(array('text' => 'Accept', 'proceedFunction' => new FunctionObject($this, 'accept'))), 'accept');
	}
}

class ErrorMessage extends Component {
	var $error_msg;

	function ErrorMessage($params) {
        $this->error_msg = $params['error_message'];
		parent::Component();
		$this->createInstance($params); // Patch...
	}

	/*function createInstance($params) {
		
	}*/

	function initialize() {
		$this->addComponent(new Label($this->error_msg),'error_message');
	}
}

?>