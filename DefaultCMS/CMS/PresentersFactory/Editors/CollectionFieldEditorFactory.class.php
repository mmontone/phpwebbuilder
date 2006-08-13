<?php

class CollectionFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		return new CollectionViewer(
			$field->collection
		);
	}
}


?>