<?php

require_once 'EditorFactory.class.php';

class IdFieldEditorFactory extends EditorFactory {
	function &componentForField(&$field){
		return new Text(new AspectAdaptor($field, 'Value'));
	}
}

class SuperFieldEditorFactory extends EditorFactory {
	function &componentForField(&$field){
		return new Text(new AspectAdaptor($field, 'Value'));
	}
}


?>