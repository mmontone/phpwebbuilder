<?php

require_once 'EditComponentFactory.class.php';

class BoolFieldEditComponentFactory extends EditComponentFactory {
	function &componentForField(&$field){
		$value = $field->getValue();
		return new CheckBox(new ValueHolder($value));
	}
}


?>