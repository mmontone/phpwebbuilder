<?php

class LambdaObject {
   var $function;

    function LambdaObject($args, $body, &$env) {
    	$this->function = lambda($args, $body, $env);
    }

    function &call() {
    	$function = $this->function;
    	return $function();
    }

    function &callWith(&$params) {
    	$function = $this->function;
    	return $function($params);
    }

    function &getValue() {
    	return $this->call();
    }

    function setValue(&$value) {
    	$this->callWith($value);
    }
}

?>