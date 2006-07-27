<?php

require_once dirname(__FILE__).'/../FieldPresenterFactory.class.php';

class EditorFactory extends FieldPresenterFactory {}

class DataFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		return new Input($field);
	}
}

?>