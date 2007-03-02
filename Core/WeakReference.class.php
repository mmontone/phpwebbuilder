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
		$this->refClass = getClass($obj);
	}
	function &getTarget(){
		global $allObjectsInMem;
		$obj =& $allObjectsInMem[$this->refId];
		#@gencheck if ($obj!=null && $this->refClass!=getClass($obj)) { echo "got ".$this->refClass ."instead of".getClass($obj);}@#
		return $obj;
	}
	function isNotNull(){
		global $allObjectsInMem;
		return (isset($allObjectsInMem[$this->refId]));
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
}

class WeakFunctionObject extends FunctionObject{
	function &fromFunctionObject(&$fo){
		$wr =& new WeakFunctionObject($fo->getTarget(), $fo->method_name, $fo->params);
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
