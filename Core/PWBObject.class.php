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
		//if (!is_array($params)) print_backtrace();
		$this->creationParams = array_merge($this->defaultValues($params),$params);
		if (!isset($params['dontCreateInstance'])){
			$this->createInstance($this->creationParams);
		}
	}
	/**
	 * Comparing
	 */
	function isA($class) {
		return getClass($this) == $class;
	}
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
	function createInstance($params){}
	function defaultValues($params){return array();}

    /* Events mechanism */

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

    function onChangeSend($call_back_selector, & $listener) {
		$this->addEventListener(array (
			'changed' => $call_back_selector
		), $listener);
	}

	function changed() {
		$this->triggerEvent('changed', $this);
	}


    function registerEventHandle(&$handle) {
    	$this->event_handles[$handle['target']->__instance_id][$handle['event']] =& $handle;
    }

    function retractInterest(&$handle) {
    	unset($this->event_listeners[$handle['event']][$handle['handle']]);
    	if (count($this->event_listeners[$handle['event']])==0){
    		unset($this->event_listeners[$handle['event']]);
    	}
    }

    function releaseHandle(&$handle) {
    	$target =& $handle['target'];
		unset ($this->event_handles[$target->__instance_id][$handle['event']]);
		if (count($this->event_handles[$target->__instance_id])==0){
			unset($this->event_handles[$target->__instance_id]);
		}
    	$target->retractInterest($handle);
    }

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

    function &copy() {
		$class = getClass($this);
		$copy =& new $class;

		// If this fails then it means that the PWBObject constructor is not being called
		assert($copy->__instance_id != $this->__instance_id);

		// Don't share the listeners
		$copy->event_listeners = array();

		return $copy;
    }

    function isAncestorOf(&$object) {
    	return in_array(getClass($this), get_superclasses(getClass($object)));
	}

    function isDescendantOf(&$object) {
		return $object->isAncestorOf($this);
    }


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


}



?>