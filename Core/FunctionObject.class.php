<?php

class FunctionObject
{
    var $target;
    var $method_name;
    var $params;

    function FunctionObject(&$target, $method_name, $params=array()) {
        $this->target =& $target;
        $this->method_name = $method_name;
        $this->params = $params;
    }

    function call() {
      	$method_name = $this->method_name;
       	eval('$ret =& '. $this->callString($method_name) . '($this->params);');
       	return $ret;
    }

    function callString($method) {
    	if ($this->target == null)
    		return $method;
    	else
       		return '$this->target->' . $method;
    }

	function hasPermissions(){
		$m = $this->method_name;
		$msg = 'check'.ucfirst($m).'Permissions';
		if (method_exists($this->target, $msg)){
			return $this->target->$msg($this->params);
		} else {
			return true;
		}
	}

    function callWith(&$params) {
		$method_name = $this->method_name;
        if (empty($this->params)) {
        	eval('$ret =& '. $this->callString($method_name) . '($params);');
        	return $ret;
        }
        else {
        	eval('$ret =& '. $this->callString($method_name) . '($params, $this->params);');
        	return $ret;
        }
    }

    function callWithWith(&$param1, &$param2) {
    	$method_name = $this->method_name;
    	if (empty($this->params)) {
        	eval('$ret =& '. $this->callString($method_name) . '($param1, $param2);');
        	return $ret;
    	}
        else {
        	eval('$ret =& '. $this->callString($method_name) . '($param1, $param2, $this->params);');
        	return $ret;
        }
    }

    /* We may want to use function objects as ValueHolders. Similar to Aspect adaptors */

    function &getValue() {
    	return $this->call();
    }

    function setValue(&$value) {
    	return $this->callWith($value);
    }
}

function &callback(&$target, $selector) {
	return new FunctionObject($target, $selector);
}

?>