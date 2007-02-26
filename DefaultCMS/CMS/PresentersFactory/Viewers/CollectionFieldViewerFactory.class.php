<?php

class CollectionFieldViewerFactory extends ViewerFactory {
	function &createInstanceFor(&$field){
		$cv =& new CollectionViewer(
			$field->collection
		);
		return $cv;
	}
}
?>