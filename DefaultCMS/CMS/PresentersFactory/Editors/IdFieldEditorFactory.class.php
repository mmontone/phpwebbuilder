<?php

require_once 'EditorFactory.class.php';

class IdFieldEditorFactory extends EditorFactory {
	function &componentForField(&$field){
		return new Text($field);
	}
}

class SuperFieldEditorFactory extends EditorFactory {
	function &componentForField(&$field){
		return new Text($field);
	}
}


?>