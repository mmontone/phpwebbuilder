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
		#@gencheck if ($obj!=null && @$this->refClass!=getClass($obj)) { echo "got ".$this->refClass ."instead of".getClass($obj);}@#
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

class WeakLambdaObject extends LambdaObject {
  var $target;

  function &fromLambdaObject(&$lam) {
    $weaken_env = $lam->env;
    $target =& $lam->getTarget();
    $null = null;
    $weaken_env['self'] =& $null;
    $wr =& new WeakLambdaObject('','',$weaken_env);
    $wr->fdef = $lam->fdef;
    $wr->setTarget($target);
    return $wr;
  }

  function call() {
    // Temporarily restore the target
    $this->env['self'] =& $this->getTarget();
    parent::call();
    $null = null;
    $this->env['self'] =& $null;
  }

  function &callWith(&$params) {
    // Temporarily restore the target
    $this->env['self'] =& $this->getTarget();
    $ret =& parent::callWith($params);
    $null = null;
    $this->env['self'] =& $null;
    return $ret;
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
