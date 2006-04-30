<?php

require_once dirname(__FILE__) . '/XMLNode.class.php';

class XMLNodeModificationsTracker extends XMLNode
{
	var $modifications;

	function XMLNodeModificationsTracker($tag_name='div',$attributes=array()) {
		parent::XMLNode($tag_name, $attributes);
		$this->reset();
	}

	function reset() {
		$this->modifications = array();
	}

	function &instantiateFor(&$component){
		$component->setView($this);
		return $this;
	}

	function flushModifications() {
		$this->reset();
		$keys = array_keys($this->childNodes);

		foreach ($keys as $key) {
			$this->childNodes[$key]->flushModifications();
		}
	}

	function create_element($tag_name, &$controller) {
		$element =& new XMLNodeModificationsTracker($tag_name);
		$element->controller =& $controller;
		return $element;
	}

	function append_child(&$child) {
		$this->modifications[] =& new AppendChildXMLNodeModification($child);
		return parent::append_child($child);
	}

	function replace_child(&$child, &$other_child) {
		$this->modifications[] =& new ReplaceChildXMLNodeModification($child, $other_child);
		return parent::replace_child($child, $other_child);
	}

	function remove_child(&$child)  {
		$this->modifications[] =& new RemoveChildXMLNodeModification($child);
		return parent::remove_child($child);
	}

	function setAttribute($attribute, $value) {
		$this->modifications[] =& new SetAttributeXMLNodeModification($attribute, $value);
		return parent::setAttribute($attribute, $value);
	}
}
?>