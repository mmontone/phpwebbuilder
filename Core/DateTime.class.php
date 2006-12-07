<?php

class DateTime extends PWBObject{

    function DateTime($date) {
    	$this->setValue($date);
    	parent::PWBObject();
    }
    function setValue($date){
    	#@check is_string($date)@#
    	$this->date = $date;
    	$this->triggerEvent('changed', $date);
    }
    function getValue(){return$this->date;}
    function &dateArray () {
        return getDate(strtotime($this->date));
    }
    function dateDiff(&$date){
        #@typecheck $date: DateTime@#
        $d1 =& $this->dateArray();
        $d2 =& $date->dateArray();
        foreach ($d1 as $ind=>$val) {
            $d3[$ind] = $val - $d2[$ind];
        }
        return $d3;
    }
    function printString(){
    	return $this->date;
    }
    function validateDate() {
    	//return ereg('([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})',$this->date, $matches) && checkdate($matches[1], $matches[2], $matches[0]);
    	return ereg('([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})',$this->date, $matches);
    }
    function before($date){
    	#@typecheck $date: DateTime@#
    	return strtotime($this->date) < strtotime($date->date);
    }

    function now(){
        return new DateTime(DateTime::format(getDate()));
    }
    function format ($date) {
        return DateTime::dateFormat($date)." ".DateTime::timeFormat($date);
    }
    function setDateArr(&$arr){
        $this->setValue($this->format($arr));
    }
    function validateTime() {
    	//return ereg('([0-9]{2})((:([0-9]{1,2})){0,2})', $this->date, $matches) && $matches[0] <'24' && $matches[1] <'60' && $matches[2] <'60';
    	return ereg('([0-9]{2})((:([0-9]{1,2})){0,2})', $this->date, $matches);
    }
    function timeformat ($date) {
        return $date["hours"].":".$date["minutes"].":".$date["seconds"];
    }

    function dateformat ($date) {
        return $date["year"]."-".$date["mon"]."-".$date["mday"];
    }
}
?>