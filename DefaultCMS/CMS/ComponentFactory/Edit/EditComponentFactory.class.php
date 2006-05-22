<?php

require_once dirname(__FILE__).'/../FieldComponentFactory.class.php';

class EditComponentFactory extends FieldComponentFactory {}

class DataFieldEditComponentFactory extends EditComponentFactory {
	function &componentForField(&$field){
		return new Input(new ValueHolder($field->value));
	}
}

?>