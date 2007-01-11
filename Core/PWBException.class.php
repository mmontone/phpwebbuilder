<?php

class PWBException extends PWBObject {
	var $message;
	var $content;
	var $backtrace;

    function createInstance($params) {
		$this->message =& $params['message'];
    	$this->content =& $params['content'];
    	$this->backtrace = debug_backtrace();
	}

    function raise(&$handler) {
    	$handler->executeWith($this);
    }

    function getMessage() {
		return $this->message;
    }

    function &getContent() {
    	return $this->content;
    }

    function isException() {
    	return true;
    }

    function accept(&$visitor) {
    	return $visitor->visitPWBException($this);
    }
}

?>