<?php

class VersionFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		$text =& new Text($field);
		return $text;
	}
}

?>