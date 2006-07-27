<?php

class VersionFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		return new Text($field);
	}
}

?>