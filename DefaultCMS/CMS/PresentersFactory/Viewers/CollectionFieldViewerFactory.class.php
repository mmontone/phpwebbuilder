<?php

class CollectionFieldViewerFactory extends ViewerFactory {
	function &componentForField(&$field){
		return new CollectionViewer(
			$field->collection
		);
	}
}
?>