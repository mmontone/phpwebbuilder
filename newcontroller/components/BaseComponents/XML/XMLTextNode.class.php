<?php
require_once dirname(__FILE__) . '/XMLNodeModificationsTracker.class.php';

class XMLTextNode extends XMLNodeModificationsTracker
{
	var $text;
	function XMLTextNode($data, &$controller) {
		parent::XMLNodeModificationsTracker();
		$this->text = $data;
		$this->controller = & $controller;
	}
	function render() {
		return '<span>' . $this->text  . '</span>';
	}
}

?>