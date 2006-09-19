<?php

class PWBObject
{
    var $event_listeners = array();
    var $listener_handle = 1;
    var $config;
    var $__instance_id;
	var $creationParams;
	var $event_handles = array();

	/**
	 * Special var, for get_subclass
	 */
	var $isClassOfPWB = true;
	/**
	 * Creation
	 */
    function PWBObject($params=array()) {
		PWBInstanceIdAssigner::assignIdTo($this);
		$this->creationParams = array_merge($this->defaultValues($params),$params);
		if (!isset($params['dontCreateInstance'])){
			$this->createInstance($this->creationParams);
		}
	}
	/**
	 * Comparing
	 */
	 /**
	 *  Returns if the object has the specified class
	 */
	function isA($class) {
		return getClass($this) == $class;
	}
	/**
	 *  Returns if the object is the same as the parameter, or a copy
	 */
	function equalTo(&$other_pwb_object) {
		return $this->__instance_id == $other_pwb_object->__instance_id;
	}
	/**
	 *  Returns if the object is the same as the parameter
	 */
	function is(&$other_pwb_object){
		$ok = $this->equalTo($other_pwb_object);
		$realid = $this->__instance_id;
		$this->__instance_id = 0;
		$ok2 = $this->equalTo($other_pwb_object);
		$this->__instance_id = $realid;
		return $ok && $ok2;
	}
	/**
	 * Performs the needed actions to create a consistent instance
	 */
	function createInstance($params){}
	/**
	 * Returns the default initialization values of the object
	 */
	function defaultValues($params){return array();}

    /* Events mechanism */
	/**
	 * Adds a listener for each of the selector=>callback elements of the array,
	 * for the specified listener
	 */

    function &addEventListener($event_specs, &$listener) {
        $handles = array();
        $callback = array();
        $i = 1;

        foreach ($event_specs as $event_selector => $event_callback) {
  			$callback[$i] =& new FunctionObject($listener, $event_callback);
  			$handles[] =& $this->addInterestIn($event_selector, $callback[$i]);
  			$i++;
        }

        return $handles;
    }
	/**
	 * Adds a listener for the event, with the specified callback function
	 */

    function &addInterestIn($event, &$function) {
    	if (!isset($this->event_listeners[$event])) {
	        $this->event_listeners[$event] = array();
        }
    	$this->event_listeners[$event][$this->listener_handle] =& $function;
    	$handle = array('event' => $event, 'handle' => $this->listener_handle, 'target' => &$this);
       	$this->listener_handle++;
    	$function->target->registerEventHandle($handle);
       	return $handle;
    }
	/**
	 * Registers a callback for the changed event
	 */

    function onChangeSend($call_back_selector, & $listener) {
		$this->addEventListener(array (
			'changed' => $call_back_selector
		), $listener);
	}
	/**
	 * Triggers a changed event
	 */

	function changed() {
		$this->triggerEvent('changed', $this);
	}
	/**
	 * Registers a handle for the event
	 */

    function registerEventHandle(&$handle) {
    	$this->event_handles[$handle['target']->__instance_id][$handle['event']] =& $handle;
    }
	/**
	 * Removes the listener associated with the handle.
	 */

    function retractInterest(&$handle) {
    	unset($this->event_listeners[$handle['event']][$handle['handle']]);
    	if (count($this->event_listeners[$handle['event']])==0){
    		unset($this->event_listeners[$handle['event']]);
    	}
    }
	/**
	 * Releases the handle, and removes the interest
	 */

    function releaseHandle(&$handle) {
    	$target =& $handle['target'];
		unset ($this->event_handles[$target->__instance_id][$handle['event']]);
		if (count($this->event_handles[$target->__instance_id])==0){
			unset($this->event_handles[$target->__instance_id]);
		}
    	$target->retractInterest($handle);
    }
	/**
	 * Triggers the event, notifying all listeners of the selector
	 */

    function triggerEvent($event_selector, &$params) {
        trigger_error('Triggering event: ' . $event_selector);
        $listeners =& $this->event_listeners[$event_selector];

		if ($listeners == null) return;

        $listener = array();
        $i = 1;

        foreach(array_keys($listeners) as $l) {
        	$listener[$i] =& $listeners[$l];
        	$listener[$i]->callWithWith($this, $params);
        	$i++;
        }
    }
	/**
	 * Event Handlres
	 */
	/**
	 * Removes all the listeners and the handles of the object
	 *
	 * Call when an object is no longer used, and needs to be freed.
	 * PHP has a garbage collectior, but listeners keep track of
	 * listened objects, and listened objects keep track of listeners,
	 * so memory leaking will occurr.
	 */

	function release() {
		foreach(array_keys($this->event_handles) as $t) {
			$target =& $this->event_handles[$t];
			foreach(array_keys($target) as $h) {
				$handle =& $target[$h];
				$this->releaseHandle($handle);
			}
		}
		foreach(array_keys($this->event_listeners) as $s) {
			$selector =& $this->event_listeners[$s];
			if(is_array($selector)){
				foreach(array_keys($selector) as $h) {
					$function =& $selector[$h];
			    	$handle = array('event' => $s, 'handle' => $h, 'target' => &$this);
			    	$function->target->releaseHandle($handle);
				}
			}
		}
	}
	/**
	 * Removes the hadler associated with the listener and selector
	 */

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
	/** Returns if the receiver is an Ancestor of the parameter */
    function isAncestorOf(&$object) {
    	return in_array(getClass($this), get_superclasses(getClass($object)));
	}
	/** Returns if the receiver is a descendant of the parameter */
    function isDescendantOf(&$object) {
		return $object->isAncestorOf($this);
    }

	/** Visitor */
    function visit(&$obj) {
        $method_name = 'visited' . $this->getClass();
        $obj->$method_name($this);
    }
	/**
	 * Helper method for template methods
	 */
    function subclassResponsibility($method_name) {
        trigger_error('Subclass responsibility');
        //debug_print_backtrace(); /* Install PHP_Compat for PHP4 */
        print_backtrace('Subclass responsibility: ' . $method_name);
        exit;
    }
	/**
	 * Overloading
	 */
    function _call($message, $arguments) {
        trigger_error('Message not understood: ' . $message);
        debug_print_backtrace(); /* Install PHP_Compat for PHP4 */
        exit;
    }


}



?>