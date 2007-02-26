<?php

class ViewerFactory extends FieldPresenterFactory {}

class DataFieldViewerFactory extends ViewerFactory {
	function &createInstanceFor(&$field) {
		$text =& new Text($field);
		return $text;
	}
}

class ValueModelViewerFactory extends ViewerFactory {
	function &createInstanceFor(&$field) {
		$text =& new Text($field);
		return $text;
	}
}

?>