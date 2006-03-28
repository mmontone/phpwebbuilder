<?php

class Observable
{
    var $event_listeners;

    /* Events mechanism */

    function addEventListener($event_specs, &$listener) {
        foreach ($event_specs as $on_event_selector => $event_callback) {
            $event_selector = ereg_replace('on([[:space:]])*','',$on_event_selector);
            if ($this->event_listeners[$event_selector] == null)
                $this->event_listeners[$event_selector] = array();
            array_push($this->event_listeners[$event_selector], array('listener' => $listener,
                                                                      'callback' => $event_callback));
        }
    }

    function triggerEvent($event_selector, $params = array()) {
        global $logger;
        $logger->log('Triggering event: ' . $event_selector);
        if ($this->event_listeners[$event_selector] == null) return;

        /* Should this be DFS or BFS? (DFS now) */
        foreach ($this->event_listeners[$event_selector] as $listener_data) {
            $callback =& $listener_data['callback'];
            $listener =& $listener_data['listener'];
            $listener->$callback($this, $params);
            $listener->triggerEvent($event_selector, $params);
        }
    }
}
?>