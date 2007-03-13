<?php

class IndexFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field) {
		$if =& new IndexFieldChooser($field);
		return $if;
	}
}

?>