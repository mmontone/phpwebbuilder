<?php

require_once 'EditorFactory.class.php';

class BoolFieldEditorFactory extends EditorFactory {
	function &componentForField(&$field){
		$value = $field->getValue();
		return new CheckBox(new ValueHolder($value));
	}
}


?>