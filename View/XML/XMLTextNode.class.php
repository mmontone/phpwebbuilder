<?php

class XMLTextNode extends XMLNodeModificationsTracker
{
	var $data;
	function XMLTextNode($data) {
		parent::XMLNodeModificationsTracker('');
		$this->data = $data;
	}

	function renderEcho() {
		echo toAjax($this->data);
	}

	function renderNonEcho() {
		return toAjax($this->data);
	}

	function printString() {
		return '<text>' . $this->data . '</text>';
	}

}

class PlainTextNode extends XMLNodeModificationsTracker
{
	var $data;
	function PlainTextNode($data) {
		parent::XMLNodeModificationsTracker('');
		$this->data = $data;
	}

	function renderEcho() {
		echo $this->data;
	}

	function renderNonEcho() {
		return $this->data;
	}

	function printString() {
		return '<text>' . $this->data . '</text>';
	}

}

?>