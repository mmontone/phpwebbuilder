<?php

require_once("EditAction.class.php");

class EditActionNumField extends EditAction {
	function showField($object) {
		$ret .= "\n                     <input type=\"text\" name=\"";
		$ret .= $this->frmName($object);
		$ret .= "\" size=\"60\" value=\"";
		$ret .= $this->field->getValue();
		$ret .= "\">";
		return $ret;
	}
}


?>
