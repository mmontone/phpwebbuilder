<?php
require_once dirname(__FILE__) . '/DataField.class.php';

class TextField extends DataField {
	var $set = null;

	function TextField($name, $isIndex=false) {
		parent :: Datafield($name, $isIndex);
		if (is_array($isIndex)) {
			$this->set = $isIndex['set'];
		}
	}

	function hasSet() {
		return $this->set != null;
	}

	function getSet() {
		return $this->set;
	}

	function & visit(& $obj) {
		return $obj->visitedTextField($this);
	}

	function SQLvalue() {
		return "'" . $this->getValue() . "'" . ", ";
	}

	function isEmpty() {
		return $this->getValue() == '';
	}
}

class TextArea extends DataField {
	function textArea($name, $isIndex=false) {
		parent :: Datafield($name, $isIndex);
	}
	function & visit(& $obj) {
		return $obj->visitedTextArea($this);
	}

	function SQLvalue() {
		return "'" . $this->getValue() . "'" . ", ";
	}
}
?>