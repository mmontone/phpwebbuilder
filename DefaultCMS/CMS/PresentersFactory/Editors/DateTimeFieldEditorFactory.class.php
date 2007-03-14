<?php

class DateTimeFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		$dt =& new DateTimeInput($field->getValue());
		return $dt;
	}
}


?>