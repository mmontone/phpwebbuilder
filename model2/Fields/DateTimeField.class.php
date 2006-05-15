<?php

require_once dirname(__FILE__) . '/DataField.class.php';

class DateTimeField extends DataField {
    function DateTimeField ($name, $isIndex) {
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
        if (ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $this->value)) {
            return $this->value;
        } else {
            $date = getDate();
            return $this->format($date);
        }
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

class DateField extends DateTimeField{
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