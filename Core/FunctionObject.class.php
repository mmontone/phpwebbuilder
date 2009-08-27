<?php
/**
 * Encapsulates a method from an object
 */
class FunctionObject
{
    var $target;
    var $method_name;
    var $params;

    function FunctionObject(&$target, $method_name, $params=array()) {
        #@gencheck
        if($target == null)
        {
        	//if(!function_exists($method_name)) { print_backtrace('Function ' . $method_name . ' does not exist');        }
        }
        else {
            if(!method_exists($target, $method_name)) { print_backtrace('Method ' . $method_name . ' does not exist in ' . getClass($target));        }
        }//@#

        $this->setTarget($target);
        $this->method_name = $method_name;
        $this->params = $params;
    }

    function &weakVersion() {
    	$wfo =& WeakFunctionObject::fromFunctionObject($this);
      return $wfo;
    }

    function getMethodName() {
    	return $this->method_name;
    }

    function getParams() {
    	return $this->params;
    }

	function setTarget(&$target){
		$this->target =& $target;
	}
	function &getTarget(){
		return $this->target;
	}

    // executePreviousMethod and executeNextMethod were originally implemented
    // to support the "current component" abstraction. See Component>>aboutToExecuteFunction.
    //                       -- marian
    function executePreviousMethod() {
        $t =& $this->getTarget();
        if (method_exists($t, 'aboutToExecuteFunction')) {
        	$t->aboutToExecuteFunction($this);
        }
    }

    function executeNextMethod() {
        $t =& $this->getTarget();
        if (method_exists($t, 'functionExecuted')) {
            $t->functionExecuted($this);
        }
    }

    function &call() {
      	$this->executePreviousMethod();
        $method_name = $this->method_name;
       	$ret = $this->catchEval($this->callString($method_name) . '($this->params);');
        $this->executeNextMethod();
       	return $ret;
    }
	function execute() {
      	$this->executePreviousMethod();
        $method_name = $this->method_name;
       	$ret = $this->catchEval($this->executeString($method_name) . '($this->params);');
        $this->executeNextMethod();
    }
	function executeWith(&$param1) {
      	$this->executePreviousMethod();
        $method_name = $this->method_name;
       	$ret = $this->catchEval($this->executeString($method_name) . '($param1, $this->params);', $param1);
        $this->executeNextMethod();
    }

	function executeWithWith(&$param1, &$param2) {
      	$this->executePreviousMethod();
        $method_name = $this->method_name;
       	$ret = $this->catchEval($this->executeString($method_name) . '($param1, $param2, $this->params);', $param1, $param2);
        $this->executeNextMethod();
    }
    function catchEval($evalString, &$param1=null, &$param2=null) {
        $ret = null;
        $ex = "Error";
        eval("try{".$evalString.'; $ex=null;}catch(Exception $e){$ex=$e;}');
        if($ex!=null){
            if ($ex=="Error"){
                throw new PWBException("Unhandled exception");
            } else {
                throw $ex;
            }
        } else {
            return $ret;
        }
    }

    function callString($method) {
    	if ($this->target === null) {
    		return '$ret =& '. $method;
    	}
    	else {
       		return '$t =& $this->getTarget(); $ret =& $t->' . $method;
    	}
    }
    function executeString($method) {
    	if ($this->target === null) {
    		return $method;
    	}
    	else {
       		return '$t =& $this->getTarget(); $t->' . $method;
    	}
    }
	/**
	 *  Permission checking
	 */
	function hasPermissions(){
		$m = $this->method_name;
		$msg = 'check'.ucfirst($m).'Permissions';
		if (method_exists($this->target, $msg)){
			return $this->target->$msg($this->params);
		} else {
			return true;
		}
	}

    function &callWith(&$param1) {
		$method_name = $this->method_name;
		$ret ='';
    	$ret = $this->catchEval($this->callString($method_name) . '($param1, $this->params);', $param1);
    	return $ret;
    }

    function &callWithWith(&$param1, &$param2) {
    	$method_name = $this->method_name;
    	$ret ='';
    	$ret = $this->catchEval($this->callString($method_name) . '($param1, $param2, $this->params);');
    	return $ret;
    }

    /* We may want to use function objects as ValueHolders. Similar to Aspect adaptors */

    function &getValue() {
    	return $this->call();
    }

    function setValue(&$value) {
    	return $this->callWith($value);
    }

    function primPrintString($str){
        return '[' . getClass($this) . ' ' . $str .']';
    }

    function printString() {
        return $this->primPrintString($this->target->printString() . '->' . $this->method_name);
    }

    function debugPrintString() {
    	return $this->primPrintString($this->target->debugPrintString() . '->' . $this->method_name);
    }
}

function &callback(&$target, $selector) {
	return new FunctionObject($target, $selector);
}

?>