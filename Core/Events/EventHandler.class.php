<?php

class EventHandler {
    var $function;
    var $event;
    var $backtrace_string;

    function EventHandler($params) {
        //$this->backtrace_string = print_r(debug_backtrace(),true);
        $this->backtrace_string = '';
        $this->initialize($params);
    }

    function initialize($params) {
    	//$this->function =& $params['function'];
        $this->function =& WeakFunctionObject::fromFunctionObject($params['function']);
        $this->event = $params['event'];
    }

    function FromParams($params) {
        if ($params['execute on triggering']) {
        	$h =& new WhenEventTriggeredHandler($params);
            return $h;
        }

        if ($params['execute once']) {
        	$h =& new DeferredAndOnceEventHandler($params);
            return $h;
        }

        $h =& new DeferredEventHandler($params);
        //$h =& new DeferredAndOnceEventHandler($params);
        //$h =& new WhenEventTriggeredHandler($params);
        return $h;
    }

    function executeWithWith($event, &$triggerer, &$params) {
    	print_backtrace_and_exit('Subclass responsibility');
    }

    function getMethodName() {
        return $this->function->getMethodName();
    }

    function getParams() {
        return $this->function->getParams();
    }

    function setTarget(&$target) {
    	$this->function->setTarget($target);
    }

    function &getTarget() {
        return $this->function->getTarget();
    }

    function printString() {
    	return '[' . getClass($this) . ' function: ' . $this->function->printString() . ' event: ' . $this->event . ']';
    }

    function isNotNull() {
    	return $this->function->isNotNull();
    }
}

?>