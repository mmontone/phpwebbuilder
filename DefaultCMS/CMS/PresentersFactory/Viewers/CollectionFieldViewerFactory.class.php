<?php

class CollectionFieldViewerFactory extends ViewerFactory {
	function &createInstanceFor(&$field){
		return new CollectionViewer(
			$field->collection
		);
	}
}
?>