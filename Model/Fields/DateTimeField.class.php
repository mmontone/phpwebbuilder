<?php

class DateTimeField extends DataField {
    function createInstance($params){
    	parent::createInstance($params);
    	$this->setValue(new DateTime(''));
    }
    function SQLvalue() {
        $d =& $this->getValue();
        return "'".$d->printString()."'" . ", " ;
    }
	function loadFrom($reg) {
		$val = $reg[$this->sqlName()];
		$this->setValue(new DateTime($val));
	}
    function &validate() {
    	$v =& $this->getValue();
    	if ((!$v->validateTime()) or (!$v->validateDate())) {
    		return new ValidationException(array('message' => 'The time or date are invalid', 'content' => &$this));
    	}
    	return false;
    }

	function setNow(){
		$this->setValue(DateTime::now());
	}
	function setValue(&$d){
		#@typecheck $d : DateTime@#
		$this->date =& $d;
		$d->onChangeSend('changed',$this);
		parent::setValue($d);
	}
}

class DateField extends DateTimeField {
    function format ($date) {
        return DateTime::dateFormat($date);
    }
    function &validate() {
    	$v =& $this->getValue();
    	if (!$v->validateDate()) {
    		return new ValidationException(array('message' => 'The date is invalid', 'content' => &$this));
    	}
    	return false;
    }
}

class TimeField extends DateTimeField{
    function format ($date) {
        return DateTime::timeFormat($date);
    }
	function &validate() {
    	$v =& $this->getValue();
    	if (!$v->validateTime()) {
    		return new ValidationException(array('message' => 'The time is invalid', 'content' => &$this));
    	}
    	return false;
    }
}

?>