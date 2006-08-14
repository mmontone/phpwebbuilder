<?php

class ViewerFactory extends FieldPresenterFactory {}

class DataFieldViewerFactory extends ViewerFactory {
	function &createInstanceFor(&$field) {
		return new Text($field);
	}
}

class ValueModelViewerFactory extends ViewerFactory {
	function &createInstanceFor(&$field) {
		return new Text($field);
	}
}

?>