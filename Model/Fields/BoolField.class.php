<?php
require_once dirname(__FILE__) . '/DataField.class.php';

class BoolField extends DataField {
	function boolField($name, $isIndex) {
		parent :: Datafield($name, $isIndex);
	}
	function SQLvalue() {
		if ($this->getValue())
			$sql_value = '1';
		else
			$sql_value = '0';
		return $sql_value . ", ";
	}

	function loadFrom($form) {
		$val = $form[$this->sqlName()];
		if ($val === "false")
			$val = 0;
		if ($val === "true")
			$val = 1;
		if ($val == null)
			$val = 0;
		$this->setValue($val);
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