<?php
class PWBObject
{
    var $event_listeners = array();
    var $listener_handle = 1;
    var $config;
    var $__instance_id = null;
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
		#@check is_array($params)@#
		$iid =& Session::getAttribute('instance_id');
		$this->__instance_id = ++$iid;
		//Session::setAttribute('instance_id', ++$iid);
		//$this->creationParams = array_merge($this->defaultValues($params),$params);
		$this->creationParams =& $params;
		$this->__wakeup();
		$this->createInstance($this->creationParams);
	}
	/**
	 * Comparing
	 */
	 /**
	 *  Returns if the object has the specified class
	 */
	function isA($class) {
		return is_a($this,$class);
	}
	function hasType($class) {
		return $this->isA($class) || $this->hasMixin($class);
	}
	function hasMixin($mixin) {
		$varname = '__use_mixin_'.$mixin;
		return isset($this->$varname);
	}
	/**
	 *  Returns if the object is the same as the parameter, or a copy
	 */
	function equalTo(&$other_pwb_object) {
		return $this->getInstanceId() == $other_pwb_object->getInstanceId();
	}
	function getInstanceId(){
		#@check !is_null($this->__instance_id)@#
		return $this->__instance_id;
	}
	function __wakeup() {
		global $allObjectsInMem;
		#@gencheck if (!$this->getInstanceId() or isset($allObjectsInMem[$this->getInstanceId()]))
        {
		  print_backtrace(getClass($this) . ' does not have ID!!');
		}//@#
		$allObjectsInMem[$this->getInstanceId()] =& $this;
	}
	/**
	 *  Returns if the object is the same as the parameter
	 */
	function is(&$other_pwb_object){
		if (!isPWBObject($other_pwb_object)) return false;
		$ok = $this->equalTo($other_pwb_object);
		$realid = $this->getInstanceId();
		$this->__instance_id = -10;
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
        #@check is_array($event_specs)@#
        $callback = array();
        $i = 1;

        foreach ($event_specs as $event_selector => $event_callback) {
  			$callback[$i] =& new FunctionObject($listener, $event_callback);
			$this->addInterestIn($event_selector, $callback[$i]);
  			$i++;
        }
    }
	/**
	 * Adds a listener for the event, with the specified callback function
	 */

    function addInterestIn($event, &$function) {
       	$this->event_listeners[$event][] =& WeakFunctionObject::fromFunctionObject($function);
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
	 * Removes the listener associated with the handle.
	 */

    function retractInterest(&$handle) {
    	unset($this->event_listeners[$handle['event']][$handle['handle']]);
    	if (count($this->event_listeners[$handle['event']])==0){
    		unset($this->event_listeners[$handle['event']]);
    	}
    }
	/**
	 * Triggers the event, notifying all listeners of the selector
	 */

    function triggerEvent($event_selector, &$params) {
        $listeners =& $this->event_listeners[$event_selector];
		if ($listeners == null) return;
        //print_backtrace('Triggering event ' . $event_selector . ' listeners: ' . count($listeners));
        foreach(array_keys($listeners) as $l) {
        	$listener =& $listeners[$l];
        	if ($listener->isNotNull()) {
        		$listener->callWithWith($this, $params);
        	} else {
				unset($listeners[$l]);
        	}
        }
    }
	/**
	 * Event Handlres
	 */
	/**
	 * Removes all the listeners and the handles of the object.
	 * (Not needed anymore)
	 *
	 */

	function release() {	}
	/**
	 * Removes the hadler associated with the listener and selector
	 */

	function retractInterestIn($event_selector, &$listener) {
    	$listeners =& $this->event_listeners[$event_selector];

		reset($listeners);
		$match = false;

		while (!$match && (list($key, $array_obj) = each($listeners))) {
		 	$match = $listener->is($array_obj['listener']->getTarget());
		 	next($listeners);
		}
		if (!$match) {
			print_backtrace('Fatal error removing listener');
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