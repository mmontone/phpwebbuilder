<?php

require_once 'FieldComponentFactory.class.php';

class ShowComponentFactory extends FieldComponentFactory {}

class DataFieldShowComponentFactory extends ShowComponentFactory {
	function &componentForField(&$field){
		return new Label($field->value);
	}
}

?>