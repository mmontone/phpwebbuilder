<?php

require_once 'EditorFactory.class.php';

class CollectionFieldEditorFactory extends EditorFactory {
	function &componentForField(&$field){
		return new CollectionViewer(
			$field->collection
		);
	}
}


?>