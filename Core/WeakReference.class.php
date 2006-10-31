<?php

$allObjectsInMem = array();

class WeakReference {
	var $refId;
	function WeakReference(&$referenced){
		$this->setTarget($referenced);
	}
	function setTarget(&$obj){
		$this->refId = $obj->getInstanceId();
		$this->refClass = getClass($obj);
	}
	function &getTarget(){
		global $allObjectsInMem;
		$obj =& $allObjectsInMem[$this->refId];
		if ($obj!=null && $this->refClass!=getClass($obj)) { echo "got ".$this->refClass ."instead of".getClass($obj);}
		return $obj;
	}
	function isNotNull(){
		global $allObjectsInMem;
		return $allObjectsInMem[$this->refId]!=null;
	}
}

class WeakFunctionObject extends FunctionObject{
	function &fromFunctionObject(&$fo){
		return new WeakFunctionObject($fo->getTarget(), $fo->method_name, $fo->params);
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
