<?php

require_once 'EditorFactory.class.php';

class TextAreaEditorFactory extends EditorFactory {
	function &componentForField(&$field){
		return new TextAreaComponent(new AspectAdaptor($field, 'Value'));
	}
}

?>