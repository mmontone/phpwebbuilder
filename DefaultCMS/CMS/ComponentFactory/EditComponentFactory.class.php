<?php

require_once 'FieldComponentFactory.class.php';

class EditComponentFactory extends FieldComponentFactory {}

class DataFieldEditComponentFactory extends EditComponentFactory {
	function &componentForField(&$field){
		return new Input(new ValueHolder($field->value));
	}
}

?>