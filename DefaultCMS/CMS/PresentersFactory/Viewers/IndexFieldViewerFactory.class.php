<?php

require_once 'ViewerFactory.class.php';

class IndexFieldViewerFactory extends ViewerFactory {
	function &componentForField(&$field){
		return new Label($field->viewValue());
	}
}

?>