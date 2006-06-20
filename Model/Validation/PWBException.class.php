<?php

class PWBException {
	var $message;

    function PWBException($message='') {
    	$this->message = $message;
    }

    function raise(&$handler) {
    	$handler->callWith($this);
    }

    function getMessage() {
		return $this->message;
    }
}
?>