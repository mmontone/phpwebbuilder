<?php

class WhenEventTriggeredHandler extends EventHandler {

    function executeWithWith(&$triggerer, &$params) {
        #@track_events echo 'Executing ' . $this->printString() . '<br/>';@#
        return $this->function->executeWithWith($triggerer, $params);
    }
}

?>