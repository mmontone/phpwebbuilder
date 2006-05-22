<?php

require_once 'ShowComponentFactory.class.php';

class IndexFieldShowComponentFactory extends ShowComponentFactory {
	var $fc;
	var $field;
	var $vh;
	function &componentForField(&$field){
		return new Label($f = $field->viewValue());
	}
}

?>