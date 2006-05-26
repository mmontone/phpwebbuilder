<?php

class Exception {
	var $message;

    function Exception($message='') {
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