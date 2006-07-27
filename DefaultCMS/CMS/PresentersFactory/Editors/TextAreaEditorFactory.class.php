<?php

class TextAreaEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		return new TextAreaComponent($field);
	}
}

?>