<?php

require_once("EditAction.class.php");

class EditActionIndexField  extends EditAction {
 	function showField ($renderer) {
		//$html = $object->viewFor($this->field->collection);
		return $html->asSelect($this->frmName($renderer), $this->field->getValue());
	}
}
?>
