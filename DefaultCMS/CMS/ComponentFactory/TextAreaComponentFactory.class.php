<?php

require_once 'EditComponentFactory.class.php';

class TextAreaEditComponentFactory extends EditComponentFactory {
	function &componentForField(&$field){
		return new TextAreaComponent(new ValueHolder($field->value));
	}
}

?>