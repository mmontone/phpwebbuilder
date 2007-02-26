<?php

class EditorFactory extends FieldPresenterFactory {}

class DataFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		$i =&  new Input($field);
		return $i;
	}
}

?>