<?php

require_once 'EditorFactory.class.php';

class CollectionFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		return new CollectionViewer(
			$field->collection
		);
	}
}


?>