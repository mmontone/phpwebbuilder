<?php

class DateTimeField extends DataField {
    function DateTimeField($name, $isIndex=null) {
   	  	parent::DataField($name, $isIndex);
   	  	$this->setValue(PWBDateTime::Now());
    }
    function createInstance($params){
    	parent::createInstance($params);
    	$this->setValue(new PWBDateTime(''));
    }
    function SQLvalue() {
        $d =& $this->getValue();
        return "'".$d->printString()."'" . ", " ;
    }
	function loadFrom($reg) {
		$val = $reg[$this->sqlName()];
		$this->setValue(new PWBDateTime($val));
	}
    function &validate() {
    	$v =& $this->getValue();
    	if ((!$v->validateTime()) or (!$v->validateDate())) {
    		return new ValidationException(array('message' => 'The time or date are invalid', 'content' => &$this));
    	}
    	$f = false;
    	return $f;
    }

	function setNow(){
		$this->setValue(PWBDateTime::now());
	}
	function setValue(&$d){
		#@typecheck $d : PWBDateTime@#
		$this->value =& $d;
		$d->onChangeSend('changed',$this);
		if ($d !== $this->buffered_value) {
			$this->buffered_value =& $d;
			$this->setModified(true);
			$this->triggerEvent('changed', $no_params = null);
		}
	}
	function &getValue() {
		if ($this->buffered_value !== null)
			return $this->buffered_value;
		else
			return $this->value;
	}
}

class DateField extends DateTimeField {
    function format ($date) {
        return PWBDateTime::dateFormat($date);
    }
    function &validate() {
    	$v =& $this->getValue();
    	if (!$v->validateDate()) {
    		return new ValidationException(array('message' => 'The date is invalid', 'content' => &$this));
    	}
    	$f = false;
    	return $f;
    }
}

class TimeField extends DateTimeField{
    function format ($date) {
        return PWBDateTime::timeFormat($date);
    }
	function &validate() {
    	$v =& $this->getValue();
    	if (!$v->validateTime()) {
    		return new ValidationException(array('message' => 'The time is invalid', 'content' => &$this));
    	}
    	$f = false;
    	return $f;
    }
}

?>