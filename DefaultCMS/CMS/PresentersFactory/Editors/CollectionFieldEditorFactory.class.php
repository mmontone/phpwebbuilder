<?php

class CollectionFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		$cv =& new CollectionViewer(
			$field->collection
		);
		return $cv;
	}
}


?>