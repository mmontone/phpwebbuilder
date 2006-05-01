<?php
require_once dirname(__FILE__) . '/XMLNodeModificationsTracker.class.php';

class XMLTextNode extends XMLNodeModificationsTracker
{
	var $text;
	function XMLTextNode($text, &$controller) {
		parent::XMLNodeModificationsTracker();
		$this->text = $text;
		$this->controller = & $controller;
	}
	function render() {
		return '<spam>' . $this->text  . '</spam>';
	}

	function setText($text) {
		$new_text_node =& new XMLTextNode($text, $this->controller);
		$this->parentNode->replace_child($this, $new_text_node);
	}
}
?>