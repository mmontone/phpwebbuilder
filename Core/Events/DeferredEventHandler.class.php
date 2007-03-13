<?php

class DeferredEventHandler extends EventHandler {
    var $triggerer;
    var $params;

    function initialize($params) {
        parent::initialize($params);
        if ($this->function->getMethodName() == 'refresh') {
        	print_backtrace_and_exit('Registering for refresh');
        }
    }

    function executeWithWith(&$triggerer, &$params) {
        $this->triggerer =& $triggerer;
        $this->params =& $params;
        $this->enqueue();
    }

    function enqueue() {
        #@track_events 'Deferring event: ' . $this->debugPrintString() . '<br/>';@#
    	global $deferredEvents;

        $deferredEvents[] =& $this;
    }

    function execute() {
    	#@track_events
        //echo 'Executing ' . getClass($this) . ' on: ' . $this->debugPrintString() . '<br/>';
        echo 'Executing ' . getClass($this) . ' on: ' . $this->debugPrintString() . ' backtrace: ' . $this->backtrace_string .'</br>';
        //@#
        return $this->function->executeWithWith($this->triggerer, $this->params);
    }

    function debugPrintString() {
        return $this->triggerer->debugPrintString() . '>>' . $this->event;
    }
}
?>