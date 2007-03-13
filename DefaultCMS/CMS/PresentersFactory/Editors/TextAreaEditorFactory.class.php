<?php

class TextAreaEditorFactory extends EditorFactory {
	function &createInstanceFor(&$field){
		$tac =&new TextAreaComponent($field);
		return $tac;
	}
}

?>