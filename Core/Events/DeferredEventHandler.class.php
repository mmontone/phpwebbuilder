<?php

class DeferredEventHandler extends EventHandler {
    var $params;

    function executeWithWith(&$triggerer, &$params) {
        $this->params =& $params;
        $this->enqueue();
    }

    function enqueue() {
        #@track_events echo 'Deferring event: ' . $this->printString() . '<br/>';@#
    	global $deferredEvents;

        $deferredEvents[] =& $this;
    }

    function execute() {
    	#@track_events echo 'Executing ' . $this->printString() .'</br>';@#
        return $this->function->executeWithWith($this->triggerer, $this->params);
    }
}
?>