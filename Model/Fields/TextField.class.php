<?php

class TextField extends DataField {
	var $set = null;
	function createInstance($params){
		parent::createInstance($params);
		$this->set = @$params['set'];
	}
	function defaultValues($params){
		return array_merge(array('set'=>null), parent::defaultValues($params));
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
		return "'" . addslashes($this->getValue()) . "'" . ", ";
	}

	function isEmpty() {
		return $this->getValue() == '';
	}
}

class TextArea extends DataField {
	function & visit(& $obj) {
		return $obj->visitedTextArea($this);
	}

	function SQLvalue() {
		return "'" .addslashes( $this->getValue()) . "'" . ", ";
	}
}
?>