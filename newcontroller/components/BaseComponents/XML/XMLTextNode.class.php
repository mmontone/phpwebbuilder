<?php
require_once dirname(__FILE__) . '/XMLNodeModificationsTracker.class.php';

class XMLTextNode extends XMLNodeModificationsTracker
{
	var $data;
	function XMLTextNode($data, &$controller) {
		parent::XMLNodeModificationsTracker();
		$this->data = $data;
		$this->controller = & $controller;
	}
	function render() {
		return '<span>' . $this->data  . '</span>';
	}
}

?>