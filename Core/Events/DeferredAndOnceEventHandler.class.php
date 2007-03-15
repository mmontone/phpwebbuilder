<?php

class DeferredAndOnceEventHandler extends DeferredEventHandler {
    function initialize($params) {
        //$this->function =& $params['function'];
        $this->function =& WeakFunctionObject::fromFunctionObject($params['function']);
        $this->event = $params['event'];
    }

    function enqueue() {
        #@track_events echo 'Deferring event (once): ' . $this->printString() . '<br/>';@#
        global $deferredAndOnceEvents;

        $key = 't_' . $this->triggerer->__instance_id;
        if (!isset($deferredAndOnceEvents['hash'][$key][$this->event])) {
            $deferredAndOnceEvents['ordered'][] = array('key' => $key, 'event' => $this->event);
        }
        @$deferredAndOnceEvents['hash'][$key][$this->event] =& $this;
    }
}
?>