<?php

class EditorFactory extends FieldPresenterFactory {}

class DataFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		return new Input($field);
	}
}

?>