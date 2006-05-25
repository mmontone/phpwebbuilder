<?php

require_once 'ShowComponentFactory.class.php';

class BoolFieldShowComponentFactory extends ShowComponentFactory {

    function &componentForField(&$field) {
    	$cb =& new CheckBox(new ValueHolder($field->value));
    	$cb->disable();
    	return $cb;
    }
}
?>