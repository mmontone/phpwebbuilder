<?php

class PWBDateTime extends PWBObject{
    var $date;
	#@use_mixin ValueModel@#
    function PWBDateTime($date='') {
    	$this->setValue($date);
    	parent::PWBObject();
    }
    function setValue($date){
    	#@check is_string($date)@#
    	if ($this->date != $date) {
            $this->date = $date;
        	$this->triggerEvent('changed', $date);
        }
    }

    function getDate() {
    	return $this->date;
    }

    function isLike(&$PWBDateTime) {
    	return is_a($PWBDateTime, 'PWBDateTime') and ($this->getDate() == $PWBDateTime->getDate());
    }
    function getValue(){return$this->date;}
    function &dateArray () {
        return getDate(strtotime($this->date));
    }
    function dateDiff(&$date){
        #@typecheck $date: PWBDateTime@#
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
    	return ereg('([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})',$this->date, $matches) && checkdate($matches[2], $matches[3], $matches[1]);
    	//return ereg('([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})',$this->date, $matches);
    }
    function before($date){
    	#@typecheck $date: PWBDateTime@#
    	return strtotime($this->date) < strtotime($date->date);
    }

    function now(){
        return new PWBDateTime(PWBDateTime::format(getDate()));
    }
    function format ($date) {
        return PWBDateTime::dateFormat($date)." ".PWBDateTime::timeFormat($date);
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