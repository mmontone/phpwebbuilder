<?php

class PWBObject
{
    var $service_request_handlers;
    var $event_listeners;
    var $service_request_listeners;

    function visit(&$obj) {
        $method_name = 'visited' . $this->get_class();
        $obj->$method_name($this);
    }
	/**
	 * Adds the $listener for the $event_specs, with the specified callbacs. 
	 */
    function addEventListener($event_specs, &$listener) {
        foreach ($event_specs as $on_event_selector => $event_callback) {
            $event_selector = ereg_replace('on([[:space:]])*','',$on_event_selector);
            if ($this->event_listeners[$event_selector] == null)
                $this->event_listeners[$event_selector] = array();
            array_push($this->event_listeners[$event_selector], array('listener' => $listener,
                                                                      'callback' => $event_callback));
        }
    }
	/**
	 * calls all needed callback methods for each listener,
	 * 		and then triggers the event of the same name on the listener!!!
	 */
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

    function addServiceRequestHandler($service_name, $handler_function) {
        $this->service_request_handlers[$service_name] = $handler_function;
    }

    function addServiceRequestListener(&$listener) {
    	array_push($this->service_request_listeners, $listener);
    }


    function triggerServiceRequest(&$service_request, &$result) {
        reset($this->service_request_listeners);
        $service_request_handled = false;
        while($listener = next($this->service_request_listeners) && !$service_request_handled) {
            $service_request_handled = $listener->handleServiceRequest($service_request, $result);
        }

        if (!$service_request_handled) {
            trigger_error('Error: ' . $service_request->id . ' couldn\'t be handled');
        }
        else {
            return true;
        }
    }

    function handleServiceRequest(&$service_request, &$result) {
        if (array_key_exists($service_request->id, array_keys($this->service_request_handlers))) {
            $handler = $this->handled_services($service_request->id);
            $result =& $this->$handler($service_request);
            return true;
        }
        else {
            return $this->triggerServiceRequest($service_request, $result);
        }
    }
}

 /*
    function triggerServiceRequest(&$service_request, &$callback) {
        reset($this->service_request_listeners);
        $service_request_handled = false;
        while($listener = next($this->service_request_listeners) && !$service_request_handled) {
            $service_request_handled = $listener->handleServiceRequest($service_request, $callback);
        }

        if (!$service_request_handled) {
            trigger_error('Error: ' . $service_request->id . ' couldn\'t be handled');
        }
        else {
            return true;
        }
    }

    function handleServiceRequest(&$service_request, &$callback) {
        if (array_key_exists($service_request->id, array_keys($this->service_request_handlers))) {
            $handler = $this->handled_services($service_request->id);
            $result =& $this->$handler($service_request);
            $callback->call(array($sevice_request->id . '_result' => $result));
            return true;
        }
        else {
            return $this->triggerServiceRequest($service_request, $callback);
        }
    }
    */
?>