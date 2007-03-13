<?php

#@php5
class PWBException extends Exception {
//@#

#@php4 class PWBException extends PWBObject {
//@#
	var $message;
	var $content;
	var $backtrace;

    #@php5
    public function __construct($params) {
        $this->createInstance($params);
        parent::__construct($params['message'], 0);
    }
    //@#

    function createInstance($params) {
		$this->message =& $params['message'];
    	$this->content =& $params['content'];
    	$this->backtrace = debug_backtrace();
	}

    #@php4
    function &raise() {
    	return $this;
    }

    function getMessage() {
		return $this->message;
    }

    function printString() {
    	return $this->primPrintString('message: ' . $this->getMessage());
    }
    //@#

    #@php5
    function &raise() {
    	throw $this;
    }
    //@#

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