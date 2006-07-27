<?php

class IndexFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field) {
		return new IndexFieldChooser($field);
	}
}

?>