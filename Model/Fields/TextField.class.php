<?php
require_once dirname(__FILE__) . '/DataField.class.php';

class TextField extends DataField {
	var $set = null;
	function createInstance($params){
		parent::createInstance($params);
		$this->set = $params['set'];
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
	function textArea($name, $isIndex) {
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