<?php

class WhenEventTriggeredHandler extends EventHandler {

    function executeWithWith(&$triggerer, &$params) {
        #@track_events echo 'Executing ' . getClass($this) . ' on: ' . $triggerer->printString() . '>>' . $this->event .' function: ' . $this->function->printString() . '<br/>';@#
        return $this->function->executeWithWith($triggerer, $params);
    }
}

?>