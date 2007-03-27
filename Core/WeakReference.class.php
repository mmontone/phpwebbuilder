<?php

$allObjectsInMem = array();

class WeakReference {
	var $refId;
	function WeakReference(&$referenced){
		if ($referenced!=null)
			$this->setTarget($referenced);
	}
	function setTarget(&$obj){
		$this->refId = $obj->getInstanceId();
		#@gencheck $this->refClass = getClass($obj); @#
	}
	function &getTarget(){
		$obj =& $GLOBALS['allObjectsInMem'][$this->refId];
		#@gencheck if ($obj!=null && $this->refClass!=getClass($obj)) { echo "got ".$this->refClass ."instead of".getClass($obj);}@#
		return $obj;
	}
	function isNotNull(){
		return (isset($GLOBALS['allObjectsInMem'][$this->refId]));
	}

    function primPrintString($str){
        return '[' . getClass($this).  ' ' . $str .']';
    }

    function printString() {
    	if ($this->isNotNull()) {
            $target =& $this->getTarget();
            $s = $target->printString();
        }
        else {
        	$s = 'No target';
        }
        return $this->primPrintString('to ' . $s);
    }

    function debugPrintString() {
        if ($this->isNotNull()) {
            $target =& $this->getTarget();
            $s = $target->debugPrintString();
        }
        else {
            $s = 'No target';
        }
        return $this->primPrintString('to ' . $s);
    }
}

class WeakFunctionObject extends FunctionObject{
	function &fromFunctionObject(&$fo){
		$wr =& new WeakFunctionObject($fo->getTarget(), $fo->getMethodName(), $fo->getParams());
		return $wr;
	}
	function setTarget(&$target){
		$this->target =& new WeakReference($target);
	}
	function &getTarget(){
		return $this->target->getTarget();
	}
	function isNotNull(){
		return $this->target->isNotNull();
	}
}

/*
class WeakFunctionObject {
    var $function;
    var $target;

    function WeakFunctionObject(&$function) {
    	$this->function =& $function;
        $this->target =& new WeakReference($function->getTarget());
        $n = null;
        $this->function->setTarget($n);
    }

    function &fromFunctionObject(&$fo){
        $wr =& new WeakFunctionObject($fo);
        return $wr;
    }

    function isNotNull(){
        return $this->target->isNotNull();
    }

    function printString() {
    	return '[' . getClass($this) . ' on: ' . $this->function->printString() . ']';
    }

    function executeWithWith(&$param1, &$param2) {
    	$this->function->setTarget($this->target->getTarget());
        $this->function->executeWithWith($param1, $param2);
        $n = null;
        $this->function->setTarget($n);
    }

    function executeWith(&$params) {
    	$this->function->setTarget($this->target->getTarget());
        $this->function->executeWith($params);
        $n = null;
        $this->function->setTarget($n);
    }
}
*/


class WeakCollection extends Collection {
	function &at($index){
		$wr =& parent::at($index);
		return $wr->getTarget();
	}
	function add(&$elem){
		parent::add(new WeakReference($elem));
	}
}

//Just Another VirtualMachine Architecture

?>
