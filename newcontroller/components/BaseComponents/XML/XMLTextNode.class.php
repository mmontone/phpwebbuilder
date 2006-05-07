<?php
require_once dirname(__FILE__) . '/XMLNodeModificationsTracker.class.php';

class XMLTextNode extends XMLNodeModificationsTracker
{
	var $data;
	function XMLTextNode($data) {
		parent::XMLNodeModificationsTracker();
		$this->data = $data;
	}

	function render() {
		return $this->data;
	}

	function printString() {
		return '<text>' . $this->data . '</text>';
	}

}

?>