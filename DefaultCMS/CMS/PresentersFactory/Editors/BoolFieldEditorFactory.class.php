<?php

require_once 'EditorFactory.class.php';

class BoolFieldEditorFactory extends EditorFactory {
	function &componentForField(&$field){
		return new CheckBox(new AspectAdaptor($field, 'Value'));
	}
}


?>