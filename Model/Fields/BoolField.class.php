<?php

class BoolField extends DataField {
	function SQLvalue() {
		if ($this->getValue())
			$sql_value = '1';
		else
			$sql_value = '0';
		return $sql_value . ", ";
	}

	function loadFrom($form) {
		$val = $form[$this->sqlName()];
		if ($val === "false" or $val == 0 or $val == '0') {
			$this->setValue(false);
		}
		else {
			$this->setValue(true);
		}

		return true;
	}

	function & visit(& $obj) {
		return $obj->visitedBoolField($this);
	}

	function isEmpty() {
		return !$this->getValue();
	}
}
?>