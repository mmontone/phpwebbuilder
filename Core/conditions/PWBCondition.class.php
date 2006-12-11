<?php

// Condition System (ala Common Lisp)

//eval(check_scope());
function check_scope() {
	$record =& get_current_activation_record();
	if ($record->ret) {
		$record->leave();
		return false;
	}
	return true;
}

//eval(cond_call('$o->save();'))
function cond_call($code) {
	return $code . ' return eval(check_scope());';
}

// eval(cond_return())
function cond_return($value = '') {
	return '$record =& get_current_activation_record();' .
	'$record->leave();
			    return ' . $value . ';';
}

class ActivationRecord {
	var $handlers;
	var $restarters;
	var $ret;

	function ActivationRecord() {
		$this->handlers = array ();
		$this->restarters = array ();
		$this->ret = false;
	}

	function enter() {
		$records = & get_activation_records();
		array_push($records, & $this);
	}

	function leave() {
		$records = & get_activation_records();
		array_pop($records);
	}

	function &addConditionHandler($condition_class, & $handler) {
		// TODO: Sort handlers from more specific to more general
		// Now the user is responsible
		$h =& new PWBConditionHandler($condition_class, $handler);
		$this->handlers[$condition_class] = & $h;
		return $h;
	}

	function &addConditionRestarter($condition_class, &$restarter) {
		// TODO: Sort restarters from more specific to more general
		// Now the user is responsible
		$r =& new PWBConditionRestarter($condition_class, $restarter);
		$this->restarters[$condition_class] = & $r;
		return $r;
	}

	function setReturn($b = true) {
		$this->ret = $b;
	}

	function &handleCondition(&$cond) {
		foreach (array_keys($this->restarters) as $r) {
			$restarter =& $this->restarters[$r];
			if ($restarter->matches($cond)) {
				$restarter->callWith($cond);
				return $restarter;
			}
		}

		foreach (array_keys($this->handlers) as $r) {
			$handler =& $this->handlers[$r];
			if ($handler->matches($cond)) {
				//print_backtrace('Handler match!!');
				$handler->callWith($cond);
				return $handler;
			}
		}

		return false;
	}
}

function & get_activation_records() {
	$records = & Session::getAttribute('activation_records');
	if ($records == null) {
		$main_record = & new ActivationRecord;
		$main_record->addConditionHandler('PWBCondition', new FunctionObject($n = null,'standard_condition_handler'));
		$records = array (
			$main_record
		);
	}
	return $records;
}

function & enter_scope() {
	$record = & new ActivationRecord;
	$record->enter();
	return $record;
}

function leave_scope() {
	$record = & get_current_activation_record();
	$record->leave();
}

function & get_current_activation_record() {
	$records = & get_activation_records();
	return $records[count($records) - 1];
}

class PWBCondition {
	var $name;
	var $message;
	var $restarts;

	function PWBCondition($params=array()) {
		$this->name = $params['name'];
		$this->message = $params['message'];
		$this->restarts = $params['restarts'];
	}

	function raise() {
		$records = & get_activation_records();

		$stop = false;
		for ($i = count($records) - 1; $i >= 0; $i--) {
			$record = & $records[$i];
			$handler =& $record->handleCondition($this);
			if (is_object($handler)) {
				if (getClass($handler) == 'pwbconditionrestarter') {
					$stop = true;
				}
				else {
					if (getClass($handler) == 'pwbconditionhandler') {
						for ($j = $i +1; $i < count($records); $i++) {
							$records[$i]->setReturn();
						}
						$stop = true;
					}
					else {
						print_backtrace_and_exit('Type error');
					}
				}
			}
			else {
				// No handler
			}


			if ($stop) {
				return;
			}
		}

		print_backtrace('This should not have ocurred :S');
	}

	function getMessage() {
		return $this->message;
	}

	function getName() {
		return $this->name;
	}
}

class PWBConditionHandler extends PWBObject {
	var $condition_class;
	var $func;

	function PWBConditionHandler($condition, &$function) {
		parent::PWBObject(array('condition' => $condition, 'function' => &$function));
	}
	function createInstance($params) {
		$this->condition_class = $params['condition'];
		$this->func = & $params['function'];
	}

	function matches(& $condition) {
		return is_a($condition, $this->condition_class);
	}

	function callWith(& $cond) {
		$this->func->callWith($cond);
	}
}

// Mmm...restarters should be strings = code?
class PWBConditionRestarter extends PWBObject {
	var $condition_class;
	var $func;

	function createInstance($params) {
		$this->condition_class = $params['condition'];
		$this->func = & $params['function'];
	}

	function matches(& $condition) {
		is_subclass_of($condition, $this->condition_class);
	}

	function callWith(& $cond) {
		$this->func->callWith($cond);
	}
}

function standard_condition_handler(& $cond) {
	print_backtrace('Fatal error: Unhandled condition: ' . getClass($cond) . ' : ' . $cond->getMessage());
}

?>
