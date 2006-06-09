<?php

require_once 'EditorFactory.class.php';

class IndexFieldEditorFactory extends EditorFactory {
	function &componentForField(&$field) {
		return new IndexFieldChooser($field);
	}
}

?>