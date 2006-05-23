<?php

require_once 'EditComponentFactory.class.php';

class BoolFieldEditComponentFactory extends EditComponentFactory {
	function &componentForField(&$field){
		return new CheckBox(new ValueHolder($field->value));
	}
}


?>