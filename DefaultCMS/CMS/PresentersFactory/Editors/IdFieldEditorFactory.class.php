<?php

class IdFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		return new Text($field);
	}
}

class SuperFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		return new Text($field);
	}
}


?>