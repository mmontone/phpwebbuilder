<?php

class PWBException extends PWBObject {
	var $message;
	var $content;

    function PWBException($params) {
    	$this->createInstance($params);
    }

	function createInstance($params) {
		$this->message =& $params['message'];
    	$this->content =& $params['content'];
	}

    function raise(&$handler) {
    	$handler->callWith($this);
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