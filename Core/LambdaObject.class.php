<?php

class LambdaObject  {
    var $function;
    var $fdef;
    var $env;
    var $id;
    var $functionName;

    function LambdaObject($args, $body, $env) {
    	if (!isset($_SESSION['lambda_object_id'])) {
    		$_SESSION['lambda_object_id'] = 1;
    	}

        $this->id = $_SESSION['lambda_object_id']++;
        $this->functionName = 'LambdaObject_' . $this->id;
        $this->env = $env;
        $this->fdef = 'function ' . $this->functionName . '(&$_self,'. $args . ') { extract($_self->env,EXTR_REFS); '. $body . '}';
    }

    function &call() {
    	$self =& $this;
        if (!function_exists($this->functionName)) {
        	eval($this->fdef);
        }

        eval('$result =& ' . $this->functionName . '($self);');

        return $result;
    }

    function &callWith(&$params) {
    	$self =& $this;
        if (!function_exists($this->functionName)) {
            eval($this->fdef);
        }

        eval('$result =& ' . $this->functionName . '($self, $params);');
        return $result;
    }

    function &getValue() {
    	return $this->call();
    }

    function setValue(&$value) {
    	$this->callWith($value);
    }
}

?>