<?php

require_once("EditAction.class.php");

class EditActionIdField extends EditAction{

	function showField ($object) {
		return "\n                     <p>&nbsp;".$this->field->value . "</p>";
	}
}
?>
