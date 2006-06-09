<?php

require_once dirname(__FILE__).'/../FieldPresenterFactory.class.php';

class EditorFactory extends FieldPresenterFactory {}

class DataFieldEditorFactory extends EditorFactory {
	function &componentForField(&$field){
		return new Input(new ValueHolder($field->value));
	}
}

?>