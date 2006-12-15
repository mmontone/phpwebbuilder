<?php

class XMLTextNode extends XMLNodeModificationsTracker
{
	var $data;
	function XMLTextNode($data) {
		parent::XMLNodeModificationsTracker('');
		$this->data = $data;
	}

	function renderEcho() {
		echo $this->renderNonEcho();
	}

	function renderNonEcho() {
		if (is_object($this->data)) {
			return toAjax($this->data->printString());
		} else {
			return toAjax($this->data);
		}
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
		echo $this->renderNonEcho();
	}

	function renderNonEcho() {
		if (is_object($this->data)) {
			return $this->data->printString();
		} else {
			return $this->data;
		}
	}

	function printString() {
		return '<text>' . $this->data . '</text>';
	}

}

?>