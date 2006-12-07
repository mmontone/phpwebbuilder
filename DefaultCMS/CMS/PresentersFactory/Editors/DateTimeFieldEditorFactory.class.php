<?php

class DateTimeFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		return new DateTimeInput($field->getValue());
	}
}


?>