<?php

class TextAreaEditorFactory extends EditorFactory {
	function &componentForField(&$field){
		return new TextAreaComponent($field);
	}
}

?>