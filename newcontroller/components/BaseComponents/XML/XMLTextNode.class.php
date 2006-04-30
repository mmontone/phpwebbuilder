<?php
require_once dirname(__FILE__) . '/XMLNode.class.php';

class XMLTextNode extends XMLNode {
	var $text;
	function XMLTextNode($text, &$controller) {
		$this->text = $text;
		$this->controller = & $controller;
	}
	function render() {
		return $this->text;
	}
}
?>