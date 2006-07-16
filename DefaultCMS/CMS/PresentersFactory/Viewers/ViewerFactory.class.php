<?php

require_once dirname(__FILE__).'/../FieldPresenterFactory.class.php';

class ViewerFactory extends FieldPresenterFactory {}

class DataFieldViewerFactory extends ViewerFactory {
	function &componentForField(&$field) {
		return new Text($field);
	}
}

?>