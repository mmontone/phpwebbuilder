<?php

class DateTimeField extends DataField {
    function DateTimeField ($name, $isIndex=false) {
        parent::Datafield($name, $isIndex);
    }

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
        if (ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $v)) {
            return $v;
        } else {
			return $this->now();
        }
    }

    function validate() {
    	if (!ereg('([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})', $this->getValue())) {
    		return new ValidationException(array('message' => $this->displayString . ' is not a valid date or time', 'content' => &$this));
    	}

    	return false;
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
    function DateField ($name, $isIndex) {
        parent::DateTimefield($name, $isIndex);
    }

    function format ($date) {
        return $this->dateFormat($date);
    }

    function &visit(&$obj) {
        return $obj->visitedDateField($this);
    }
}

class TimeField extends DateTimeField{
    function TimeField ($name, $isIndex) {
        parent::DateTimefield($name, $isIndex);
    }

    function format ($date) {
        return $this->timeFormat($date);
    }

    function &visit(&$obj) {
        return $obj->visitedTimeField($this);
    }
}

?>