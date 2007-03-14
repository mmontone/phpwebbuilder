<?php

$deferredEvents = array();
$deferredAndOnceEvents = array('ordered' => array(), 'hash' => array());
#@track_events $triggeredEvents = 0; @#

class EventHandler {
    var $function;
    var $event;
    var $backtrace_string;

    function EventHandler($params) {
        //$this->backtrace_string = print_r(debug_backtrace(),true);
        $this->backtrace_string = '';
        $this->initialize($params);
    }

    function initialize($params) {
    	//$this->function =& $params['function'];
        $this->function =& WeakFunctionObject::fromFunctionObject($params['function']);
        $this->event = $params['event'];
    }

    function FromParams($params) {
        if ($params['execute on triggering']) {
        	$h =& new WhenEventTriggeredHandler($params);
            return $h;
        }

        if ($params['execute once']) {
        	$h =& new DeferredAndOnceEventHandler($params);
            return $h;
        }

        $h =& new DeferredEventHandler($params);
        //$h =& new DeferredAndOnceEventHandler($params);
        //$h =& new WhenEventTriggeredHandler($params);
        return $h;
    }

    function executeWithWith($event, &$triggerer, &$params) {
    	print_backtrace_and_exit('Subclass responsibility');
    }

    function getMethodName() {
        return $this->function->getMethodName();
    }

    function getParams() {
        return $this->function->getParams();
    }

    function setTarget(&$target) {
    	$this->function->setTarget($target);
    }

    function &getTarget() {
        return $this->function->getTarget();
    }

    function printString() {
    	return '[' . getClass($this) . ' function: ' . $this->function->printString() . ' event: ' . $this->event . ']';
    }

    function isNotNull() {
    	return $this->function->isNotNull();
    }

    function ExecuteDeferredEvents() {
        global $deferredEvents;
        global $deferredAndOnceEvents;

        #@track_events $count = 0; @#
        while (!(empty($deferredEvents) and empty($deferredAndOnceEvents['ordered']))) {
            while (!empty($deferredEvents)) {
                $ks = array_keys($deferredEvents);
                $handler =& $deferredEvents[$ks[0]];
                unset($deferredEvents[$ks[0]]);
                $handler->execute();
                #@track_events $count++; @#
            }

            while (!empty($deferredAndOnceEvents['ordered'])) {
                $ks = array_keys($deferredAndOnceEvents['ordered']);

                $arr =& $deferredAndOnceEvents['ordered'][$ks[0]];
                $key = $arr['key'];
                $event = $arr['event'];

                unset($deferredAndOnceEvents['ordered'][$ks[0]]);
                $handler =& $deferredAndOnceEvents['hash'][$key][$event];
                unset($deferredAndOnceEvents['hash'][$key][$event]);
                $handler->execute();
                #@track_events $count++; @#
            }
        #@track_events echo 'Executed '. $count . ' deferred events in total<br/>';@#
        }
    }
}

?>