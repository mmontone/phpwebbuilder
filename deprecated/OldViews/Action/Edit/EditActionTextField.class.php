<?php

require_once("EditAction.class.php");

class EditActionTextField extends EditAction{
	function showField($object) {
		$ret = "\n                     <input type=\"text\" name=\"";
		$ret .= $this->frmName($object);
		$ret .= "\" value=\"";
		$ret .= $this->field->getValue();
		$ret .= "\">";
		return $ret;
	}
}

?>
