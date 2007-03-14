<?php

class BoolFieldEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		$bf =&new CheckBox($field);
		return  $bf;
	}
}


?>