<?php

class IndexFieldViewerFactory extends ViewerFactory {
	function &createInstanceFor(&$field){
		return new Label($field->viewValue());
	}
}

?>