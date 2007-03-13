<?php

class IndexFieldViewerFactory extends ViewerFactory {
	function &createInstanceFor(&$field){
		$l=&new Label($field->viewValue());
		return $l;
	}
}

?>