<?php

require_once 'ShowComponentFactory.class.php';

class IndexFieldShowComponentFactory extends ShowComponentFactory {
	function &componentForField(&$field){
		return new Label($f = $field->viewValue());
	}
}

?>