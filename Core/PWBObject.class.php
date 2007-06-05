<?php

$allObjectsInMem = array();

class PWBObject
{
    var $disabled_events = array();
    var $__instance_id = null;
    //var $creationParams;
    var $event_handles = 0;


	/**
	 * Special var, for get_subclass
	 */
	var $isClassOfPWB = true;
	/**
	 * Creation
	 */
    function PWBObject($params=array()) {
		#@gencheck if (!is_array($params)) echo getClass($this). ' was not initialized by an array<br/>';@#
		if (!Session::isStarted()) {
			$iid=count(@$GLOBALS['allObjectsInMem'])-1000;
		} else {
			$iid =& Session::getAttribute('instance_id');
		}
		$this->__instance_id = ++$iid;
		//$this->creationParams =& $params;
		$this->__wakeup();
		$this->createInstance($params);
	}

    function disableEvent($event) {
        @$this->disabled_events[$event]++;
        return $event;
    }

    function disableEvents($events) {
        foreach ($events as $event) {
            $this->disableEvent($event);
        }

        return $events;
    }

    function enableEvent($event) {
        $this->disabled_events[$event]--;
    }

    function enableEvents($events) {
        foreach ($events as $event) {
            $this->enableEvent($event);
        }
    }

    function isEnabledEvent($event) {
        return @$this->disabled_events[$event] == 0;
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
		return is_a($this,$class) or $this->hasMixin($class);
	}
	function hasMixin($mixin) {
		$varname = '__use_mixin_'.$mixin;
		return isset($this->$varname);
	}
	function getAllTypes(){
		return array_merge($this->getTypes(), get_superclasses(getClass($this)));
	}
	function getTypes(){
		$c =& $GLOBALS['types'][getClass($this)];
		if ($c===null){
			$arr = array();
			foreach(array_keys(get_class_vars(getClass($this))) as $k=>$v){
				if (substr($v, 0,12)=='__use_mixin_')
					$arr[$k]=strtolower(substr($v, 12));
			}
			$c = array_merge(array(getClass($this)), $arr);
		}
		return $c;
	}
	/**
	 *  Returns if the object is the same as the parameter, or a copy
	 */
	function equalTo(&$other_pwb_object) {
		return $this->getInstanceId() == $other_pwb_object->getInstanceId();
	}
	function getInstanceId(){
		#@gencheck if($this->__instance_id===null) print_backtrace(getClass($this).' doesn\'t have an id');@#
		return $this->__instance_id;
	}
	function __wakeup() {
		$id = $this->getInstanceId();
		#@gencheck if (isset( $GLOBALS['allObjectsInMem'][$id]) && !$this->is( $GLOBALS['allObjectsInMem'][$this->getInstanceId()])) print_backtrace('In position '.$this->getInstanceId(). ' there is a ' . $GLOBALS['allObjectsInMem'][$id]->printString(). ' instead of a '.$this->printString());@#
		$GLOBALS['allObjectsInMem'][$id] =& $this;
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

    function addEventListener($event_specs, &$listener) {
        #@check is_array($event_specs)@#
        foreach ($event_specs as $event_selector => $event_callback) {
			$this->addInterestIn($event_selector, new FunctionObject($listener, $event_callback));
        }
    }
	/**
	 * Adds a listener for the event, with the specified callback function
	 */

    function makeHandle(&$function) {
    	$object =& $function->getTarget();
        if (is_object($object)) {
            return 't_' . $object->__instance_id . $function->getMethodName();
        }
        else {
        	return ++$this->event_handles;
        }

    }

    function addInterestIn($event, &$function, $params = array()) {
       	$target =& $function->getTarget();
       	$handle = $this->makeHandle($function);
        if (isset($this->event_listeners[$event][$handle])) {
            if (!isset($params['force'])) {
                #@track_events2 echo 'Avoiding adding interest in ' .  $this->printString() . '>>' .$event . $function->printString() . '<br/>';@#
                return;
            }
            else {
                $handle = ++$this->event_handles;
            }
        }

        #@track_events2 echo 'Adding interest in ' .  $this->printString() . '>>' .$event . $function->printString() . ' handle: ' . $handle . '<br/>';@#
        $params['function'] =& $function;
        $params['event'] = $event;
        $params['triggerer'] =& $this;
        $event_handler =& EventHandler::FromParams($params);
        $this->event_listeners[$event][$handle] =& $event_handler;
        return $handle;
    }
    function retractInterestIn($event, &$function){
		$handle = $this->makeHandle($function);
        #@track_events2
        if (@isset($this->event_listeners[$event][$handle])) {
        	$s = $this->event_listeners[$event][$handle]->printString();
        }
        echo 'Retracting interest in ' .  $this->printString() . '>>' .$event .  $s . '<br/>';
        //@#

        unset($this->event_listeners[$event][$handle]);
    }
	/**
	 * Registers a callback for the changed event
	 */
    function onChangeSend($call_back_selector, & $listener) {
		$this->addInterestIn('changed',new FunctionObject($listener, $call_back_selector));
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
        if (!$this->isEnabledEvent($event_selector)) {
            #@track_events3 echo 'The event is disabled: ' . $event_selector  .' in: ' . $this->printString() . '<br/>';@#
            return;
        } else {
        	$this->doTriggerEvent($event_selector, $params);
        }
    }

    function doTriggerEvent($event_selector, &$params){
        $listeners =& $this->event_listeners[$event_selector];

        #@track_events global $triggeredEvents; $triggeredEvents++;@#
        #@track_events2 echo 'Triggering event: ' . $event_selector . ' in '. $this->printString() . '<br/>'; //@#

        if ($listeners == null) return;


        foreach(array_keys($listeners) as $l) {
          	$listener =& $listeners[$l];
            if ($listener->isNotNull()) {
                $listener->executeWithWith($this, $params);
        	} else {
				#@track_events2 echo 'Unsetting listener ' . $listener->printString() . '<br/>';@#
                unset($listeners[$l]);
        	}
        }
    }
	/**
	 * Event Handlers
	 */
	/**
	 * Removes all the listeners and the handles of the object.
	 * (Not needed anymore)
	 *
	 */

	function release() {	}

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
        print_backtrace_and_exit('Subclass responsibility: ' . $method_name);
    }
	/**
	 * Overloading
	 */
    function _call($message, $arguments) {
        trigger_error('Message not understood: ' . $message);
        debug_print_backtrace(); /* Install PHP_Compat for PHP4 */
        exit;
    }
	function primPrintString($str=''){
		if ($str !== '') {
			$str = ' ' . $str;
		}
        return '[' . getClass($this). ':'.$this->getInstanceId() . $str .']';
	}

    function printString() {
    	return $this->debugPrintString();
    }

    function debugPrintString() {
    	return $this->primPrintString();
    }

    function getType() {
    	return getClass($this);
    }
}
?>