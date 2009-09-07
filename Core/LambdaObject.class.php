<?php
class LambdaObject {
	var $function;
	var $fdef;
	var $env;
	var $id;
	var $functionName;

	function LambdaObject($args, $body, $env = array ()) {
		if (!isset ($_SESSION['lambda_object_id'])) {
			$_SESSION['lambda_object_id'] = 1;
		}

		$this->id = $_SESSION['lambda_object_id']++;
		$this->functionName = 'LambdaObject_' . $this->id;
		$this->env = $env;

		if ($args == '') {
			$largs = '&$_self';
		} else {
			$largs = '&$_self, ' . $args;
		}

		$this->fdef = 'function ' . $this->functionName . '(' . $largs . ') { extract($_self->env,EXTR_REFS); ' . $body . '}';
	}

	function & getTarget() {
		// I assume the client uses 'self' to indicate the "target" object
		return $this->env['self'];
	}

	function & weakVersion() {
		$lo =& WeakLambdaObject :: fromLambdaObject($this);
		return $lo;
	}

	function & call() {
		if (!function_exists($this->functionName)) {
			eval ($this->fdef);
		}
		$result =& FunctionObject::catchEval('$ret =& ' . $this->functionName . '($param1);', $this);
		return $result;
	}

	function & callWith(& $params) {
		$_self = & $this;

		if (!function_exists($this->functionName)) {
			eval ($this->fdef);

		}
        $result =& FunctionObject::catchEval('$ret =& ' . $this->functionName . '($param1, $param2);', $_self, $params);
		return $result;
	}

	function execute() {
		$this->call();
	}

	function executeWith(& $params) {
		$this->callWith($params);
	}

	function & getValue() {
		return $this->call();
	}

	function setValue(& $value) {
		$this->callWith($value);
	}

	function printString() {
		return $this->debugPrintString();
	}

	function debugPrintString() {
		return '[' . ucfirst(get_class($this)) . ' ' . $this->fdef . ']';
	}
}
?>