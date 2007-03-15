<?php

class DeferredEventHandler extends EventHandler {
    var $triggerer;
    var $params;

    function initialize($params) {
        parent::initialize($params);
        /*if ($this->function->getMethodName() == 'refresh') {
        	print_backtrace_and_exit('Registering for refresh');
        }*/
    }

    function executeWithWith(&$triggerer, &$params) {
        $this->triggerer =& $triggerer;
        $this->params =& $params;
        $this->enqueue();
    }

    function enqueue() {
        #@track_events 'Deferring event: ' . $this->printString() . '<br/>';@#
    	global $deferredEvents;

        $deferredEvents[] =& $this;
    }

    function execute() {
    	#@track_events
        echo 'Executing ' . getClass($this) . ' on: ' . $this->printString() .'</br>';
        //@#
        return $this->function->executeWithWith($this->triggerer, $this->params);
    }
}
?>