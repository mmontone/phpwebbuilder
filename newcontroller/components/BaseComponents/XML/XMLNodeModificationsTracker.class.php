<?php
require_once dirname(__FILE__) . '/XMLNode.class.php';

class XMLNodeModificationsTracker extends XMLNode {
	var $modifications;

	function XMLNodeModificationsTracker($tag_name = 'div', $attributes = array ()) {
		parent :: XMLNode($tag_name, $attributes);
		$this->modifications = array();
	}

	function & instantiateFor(& $component) {
		$component->setView($this);
		return $this;
	}

	function flushModifications() {
		$this->modifications = array();
		foreach (array_keys($this->childNodes) as $key) {
			$this->childNodes[$key]->flushModifications();
		}
	}

	function create_element($tag_name, & $controller) {
		$element = & new XMLNodeModificationsTracker($tag_name);
		$element->controller = & $controller;
		return $element;
	}

	function append_child(& $child) {
		// I don't want modifications on the $child to be taken into account by the page renderer
		$child->flushModifications();
		// Tag the child as an appended  node. See what happens when a "append" modification is found in AjaxPageRenderer
		$this->modifications[] = & new AppendChildXMLNodeModification($this, $child);
		return parent :: append_child($child);
	}

	function replace_child(& $new_child, & $old_child) {
		// I don't want modifications on the $new_child to be taken into account by the page renderer
		$new_child->flushModifications();
		// Tag the new_child as a replaced node. See what happens when a "replace" modification is found in AjaxPageRenderer
		$new_child->modifications[] = & new ReplaceChildXMLNodeModification($new_child, $old_child, $this);
		return parent :: replace_child($new_child, $old_child);
	}

	function remove_child(& $child) {
		$this->modifications[] = & new RemoveChildXMLNodeModification($this, $child);
		return parent :: remove_child($child);
	}

	function setAttribute($attribute, $value) {
		$this->modifications[] = & new SetAttributeXMLNodeModification($this, $attribute, $value);
		return parent :: setAttribute($attribute, $value);
	}

	function printString() {
		$this->getRealId();
		$attrs = "";
		foreach ($this->attributes as $name => $val) {
			$attrs .= ' ' . $name . '="' . $val . '"';
		}
		$mods = $this->printModifications();

		if (count($this->childNodes) == 0) {
			$ret = "<$this->tagName $attrs>";
			$ret .= "\n   <modifications>";
			$ret .= "\n      $mods";
			$ret .= "\n   </modifications>\n";
			$ret .="</$this->tagName>";
		}
		else {
			$childs = '';
			$ks = array_keys($this->childNodes);
			foreach ($ks as $k) {
				$childs .= $this->childNodes[$k]->printString();
			}
			$childs = str_replace("\n", "\n   ", $childs);
			$ret .= "\n<$this->tagName $attrs><children>$childs</children>\n";
			$ret .= "\n   <modifications>";
			$ret .= "\n      $mods";
			$ret .= "\n   </modifications>\n";
			$ret .= "</$this->tagName>\n";
		}

		return $ret;
	}

	function printModifications() {
		foreach (array_keys($this->modifications) as $mod) {
			$ret .= $this->modifications[$mod]->printString($this);
			$ret .= "\n";
		}
		return $ret;
	}
}
?>