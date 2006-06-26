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
       	return $this->target->$method_name($this->params);
    }

    function callWith(&$params) {
		$method_name = $this->method_name;
        if (empty($this->params))
        	return $this->target->$method_name($params);
        else
        	return $this->target->$method_name($params, $this->params);

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