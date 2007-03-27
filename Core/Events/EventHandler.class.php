<?php

$deferredEvents = array();
$deferredAndOnceEvents = array('ordered' => array(), 'hash' => array());
#@track_events $triggeredEvents = 0; @#

class EventHandler {
    var $function;
    var $event;
    var $backtrace_string;
    var $triggerer;

    function EventHandler($params) {
        //$this->backtrace_string = print_r(debug_backtrace(),true);
        $this->backtrace_string = '';
        $this->initialize($params);
    }

    function initialize($params) {
    	$this->function =& WeakFunctionObject::fromFunctionObject($params['function']);
        $this->event = $params['event'];
        $this->triggerer =& $params['triggerer'];
    }

    function &FromParams($params) {
        if (isset($params['execute on triggering'])) {
        	$h =& new WhenEventTriggeredHandler($params);
            return $h;
        }

        if (isset($params['execute once'])) {
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

    function getEvent() {
    	return $this->event;
    }

    function printString() {
    	return '[' . getClass($this) . ' event: ' . $this->triggerer->debugPrintString() . '>>' .  $this->event . ' function: ' . $this->function->debugPrintString() . ']';
    }

    function isNotNull() {
    	return $this->function->isNotNull();
    }

    function ExecuteDeferredEvents() {
        $deferredEvents=& $GLOBALS['deferredEvents'];
        $deferredAndOnceEvents =& $GLOBALS['deferredAndOnceEvents'];
        $deferredAndOnceEventsOrdered =& $deferredAndOnceEvents['ordered'];

        #@track_events $count = 0; @#
        while (!(empty($deferredEvents) and empty($deferredAndOnceEventsOrdered))) {
            while (!empty($deferredEvents)) {
                $ks = array_keys($deferredEvents);
                $handler =& $deferredEvents[$ks[0]];
                unset($deferredEvents[$ks[0]]);
                $handler->execute();
                #@track_events $count++; @#
            }

            while (!empty($deferredAndOnceEventsOrdered)) {
                $ks = array_keys($deferredAndOnceEventsOrdered);

                $arr =& $deferredAndOnceEventsOrdered[$ks[0]];
                $key = $arr['key'];
                $event = $arr['event'];

                unset($deferredAndOnceEventsOrdered[$ks[0]]);
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