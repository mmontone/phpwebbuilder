<?php

class VersionFieldEditorFactory extends EditorFactory {
	function &componentForField(&$field){
		return new Text($field);
	}
}

?>