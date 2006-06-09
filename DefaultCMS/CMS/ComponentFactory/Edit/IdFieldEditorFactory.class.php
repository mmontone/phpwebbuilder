<?php

require_once 'EditorFactory.class.php';

class IdFieldEditorFactory extends EditorFactory {
	function &componentForField(&$field){
		return new Text(new ValueHolder($field->value));
	}
}


?>