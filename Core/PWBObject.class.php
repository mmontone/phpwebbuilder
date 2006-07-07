<?php

class PWBObject
{
    var $service_request_handlers;
    var $event_listeners;
    var $service_request_listeners;
    var $config;
    var $__instance_id;
	var $creationParams;
	/**
	 * Special var, for get_subclass
	 */
	var $isClassOfPWB = true;
    function PWBObject($params=array()) {
		$id_assigner =& PWBInstanceIdAssigner::instance();
		$id_assigner->assignIdTo($this);
		$this->creationParams =& $params;
		$this->createInstance();
	}
	function createInstance(){}
	function equalTo(&$other_pwb_object) {
		return $this->__instance_id == $other_pwb_object->__instance_id;
	}
	function is(&$other_pwb_object){
		$ok = $this->equalTo($other_pwb_object);
		$realid = $this->__instance_id;
		$this->__instance_id = 0;
		$ok2 = $this->equalTo($other_pwb_object);
		$this->__instance_id = $realid;
		return $ok && $ok2;
	}
	function retractInterestIn($event_selector, &$listener) {
    	$listeners =& $this->event_listeners[$event_selector];

		reset($listeners);
		$match = false;

		while (!$match && (list($key, $array_obj) = each($listeners))) {
		 	$match = $array_obj['listener']->equalTo($listener);
		 	next($listeners);
		}


		if (!$match) {
			print_backtrace('Fatal error removing listener');
			print_r($listeners);
			exit;
		}

		while (list($next_key, $array_obj) = each($listeners)) {
			$listeners[$key] =& $listeners[$next_key];
			$key = $next_key;
			next($listeners);
		}

		unset($listeners[$key]);
    }

    /* Useful methods */

    function visit(&$obj) {
        $method_name = 'visited' . $this->getClass();
        $obj->$method_name($this);
    }

    function subclassResponsibility($method_name) {
        trigger_error('Subclass responsibility');
        //debug_print_backtrace(); /* Install PHP_Compat for PHP4 */
        print_backtrace('Subclass responsibility: ' . $method_name);
        exit;
    }

    function _call($message, $arguments) {
        trigger_error('Message not understood: ' . $message);
        debug_print_backtrace(); /* Install PHP_Compat for PHP4 */
        exit;
    }

    /* Events mechanism */

    function addEventListener($event_specs, &$listener) {
        foreach ($event_specs as $event_selector => $event_callback) {
  			$callback =& new FunctionObject($listener, $event_callback);
  			$this->addInterestIn($event_selector, $callback);
        }
    }

    function addInterestIn($event, &$function) {
    	if (!isset($this->event_listeners[$event])) {
	        $this->event_listeners[$event] = array();
        }
    	$this->event_listeners[$event][] =& $function;
    }

    function triggerEvent($event_selector, &$params) {
        trigger_error('Triggering event: ' . $event_selector);
        $listeners =& $this->event_listeners[$event_selector];

		if ($listeners == null) return;

        foreach(array_keys($listeners) as $l) {
        	$listener =& $listeners[$l];
        	$listener->callWithWith($this, $params);
        }
    }

    /* Services mechanism */

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


    /* Parameters management */

    function readParam($key, &$params) {
        if (!array_key_exists($key, $params)) {
           trigger_error("$key parameter not passed");
           debug_print_backtrace(); /* Install PHP_Compat for PHP4 */
           exit;
        }

        return $params[$key];
    }

    function &defaultParamValue(&$param, $value) {
        if ($param == null)
            return $value;
        else
            return $param;
    }

    function &copy() {
		$class = getClass($this);
		$copy =& new $class;

		// If this fails then it means that the PWBObject constructor is not being called
		assert($copy->__instance_id != $this->__instance_id);

		// Don't share the listeners
		$copy->event_listeners = array();

		return $copy;
    }
}



?>