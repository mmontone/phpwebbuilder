<?php

class IdFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		$text =& new Text($field);
		return $text;
	}
}

class SuperFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		$text =& new Text($field);
		return $text;
	}
}


?>