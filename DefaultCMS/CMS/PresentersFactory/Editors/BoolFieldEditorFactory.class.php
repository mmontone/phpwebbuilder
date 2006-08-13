<?php

class BoolFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		return new CheckBox($field);
	}
}


?>