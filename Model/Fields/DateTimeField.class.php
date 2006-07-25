<?php

class DateTimeField extends DataField {
    function SQLvalue() {
        return "'".$this->getValue()."'" . ", " ;
    }

    function &dateArray () {
        return getDate(strtotime($this->getValue()));
    }

    function setValueArr(&$arr){
        $this->setValue($this->format($arr));
    }

    function dateDiff(&$datefield){
        $d1 =& $this->dateArray();
        $d2 =& $datefield->dateArray();
        foreach ($d1 as $ind=>$val) {
            $d3[$ind] = $val - $d2[$ind];
        }
        return $d3;
    }

    function getValue() {
    	$v = parent::getValue();
        if ($v!=null) {
            return $v;
        } else {
			return $this->now();
        }
    }

    function validateDate($d) {
    	return ereg('([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})', $d);
    }

    function validate() {
    	$v = $this->getValue();
    	if ((!$this->validateTime($v)) or (!$this->validateDate($v))) {
    		return new ValidationException(array('message' => 'The time or date are invalid', 'content' => &$this));
    	}
    	return false;

    }
    function validateTime($t) {
    	return ereg('([0-9]{2})((:([0-9]{1,2})){0,2})', $t);
    }
    function now(){
        return $this->format(getDate());
    }

    function format ($date) {
        return $this->dateFormat($date)." ".$this->timeFormat($date);
    }

    function timeformat ($date) {
        return $date["hours"].":".$date["minutes"].":".$date["seconds"];
    }

    function dateformat ($date) {
        return $date["year"]."-".$date["mon"]."-".$date["mday"];
    }

    function &visit(&$obj) {
        return $obj->visitedDateTimeField($this);
    }
}

class DateField extends DateTimeField {
    function format ($date) {
        return $this->dateFormat($date);
    }

    function &visit(&$obj) {
        return $obj->visitedDateField($this);
    }

    function validate() {
    	$v = $this->getValue();
    	if (!$this->validateDate($v)) {
    		return new ValidationException(array('message' => 'The date is invalid', 'content' => &$this));
    	}
    	return false;
    }
}

class TimeField extends DateTimeField{
    function format ($date) {
        return $this->timeFormat($date);
    }

    function &visit(&$obj) {
        return $obj->visitedTimeField($this);
    }
	function &validate() {
    	$v = $this->getValue();
    	if (!$this->validateTime($v)) {
    		return new ValidationException(array('message' => 'The time is invalid', 'content' => &$this));
    	}
    	return false;
    }
}

?>