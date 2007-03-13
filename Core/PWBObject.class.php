<?php

$allObjectsInMem = array();

class PWBObject
{
    //var $event_listeners = array();
    var $disabled_events = array();
    var $__instance_id = null;
    var $creationParams;


	/**
	 * Special var, for get_subclass
	 */
	var $isClassOfPWB = true;
	/**
	 * Creation
	 */
    function PWBObject($params=array()) {
		#@check is_array($params)@#
		if (!Session::isStarted()) {
			$iid=count(@$GLOBALS['allObjectsInMem'])-1000;
		} else {
			$iid =& Session::getAttribute('instance_id');
		}
		$this->__instance_id = ++$iid;
		$this->creationParams =& $params;
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
	function &getInstanceId(){
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

    function addInterestIn($event, &$function, $params = array()) {
       	#@track_events echo 'Adding interest in ' .  $this->printString() . '>>' .$event . $function->printString() . '<br/>';@#
       	$i=@$this->event_handlers[$event]++;
        //$this->event_listeners[$event][$i] =& WeakFunctionObject::fromFunctionObject($function);
        $params['function'] =& $function;
        $params['event'] = $event;
        $event_handler =& EventHandler::FromParams($params);
        $this->event_listeners[$event][$i] =& $event_handler;
        //$this->event_listeners[$event][$i] =& WeakFunctionObject::fromFunctionObject($event_handler);
        return $i;
    }
    function retractInterestIn($event, $handle){
		#@track_events
        if (isset($this->event_listeners[$event][$handle])) {
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
            #@track_events //echo 'The event is disabled: ' . $event_selector  .' in: ' . $this->printString() . '<br/>';@#
            return;
        } else {
        	$this->doTriggerEvent($event_selector, $params);
        }
        /*global $events_triggered;
        if (!isset($events_triggered[$event_selector])){
        	@$events_triggered[$event_selector][getClass($this)] = 1;
        } else {
        	@$events_triggered[$event_selector][getClass($this)]++;
        }*/
    }
    function doTriggerEvent($event_selector, &$params){
        $listeners =& $this->event_listeners[$event_selector];

        #@track_events
        global $triggeredEvents;
        $triggeredEvents++;
        echo 'Triggering event: ' . $event_selector . ' in '. $this->printString() . '<br/>';
        //@#

        if ($listeners == null) return;


        foreach(array_keys($listeners) as $l) {
          	$listener =& $listeners[$l];
            if ($listener->isNotNull()) {
                $listener->executeWithWith($this, $params);
        	} else {
				#@track_events echo 'Unsetting listener ' . $listener->printString() . '<br/>';@#
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
}
?>