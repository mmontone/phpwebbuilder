<?php
class DOMXMLNode //extends PWBObject
{
	var $childNodes = array ();
	var $parentNode = null;
	var $tagName = "div";
	var $attributes = array ();
	var $parentPosition = null;
	var $nextNode = 0;
	var $fullPath = null;

	function DOMXMLNode($tag_name = "div", $attributes = array ()) {
		$this->tagName = $tag_name;
		$this->attributes = $attributes;
		$this->nextNode = 0;
	}

	function & create_element($tag_name) {
		$class_name = get_class($this);
		$element = & new $class_name($tag_name);
		return $element;
	}

	function parentNode() {
		if ($this->parentNode != null) {
			return $this->parentNode;
		}
		else {
			print_backtrace("there is no parent");
		}
	}

	function setTagName($tagName) {
		$this->tagName = $tagName;
	}

	function & create_text_node($text, & $obj) {
		return new XMLTextNode($text, & $obj);
	}
	function & first_child() {
		return $this->childNodes[0];
	}
	function append_child(& $xml) {
		$this->insert_in($xml,$this->nextNode++);
		/*$this->triggerEvent('childAppended', array (
			'child' => $xml
		));*/
	}
	function insert_in(& $xml, $position) {
		$this->childNodes[$position] = & $xml;
		$xml->parent = & $this;
		$xml->parentPosition = $position;
		$xml->updateFullPath();
	}

	function updateFullPath() {
		$this->fullPath = $this->parent->fullPath . '/'. $this->parentPosition;

		foreach (array_keys($this->childNodes) as $i) {
			$this->childNodes[$i]->updateFullPath();
		}
	}

	function replace_child(& $old, & $new) {
		$this->insert_in($new, $old->parentPosition);
		$n = null;
		$old->parent = & $n;
		$old->parentPosition = & $n;
		/*$this->triggerEvent('childReplaced', array (
			'target' => $old,
			'replacement' => $new
		));*/
	}
	function remove_child(& $old) {
		$pos = $old->parentPosition;
		$last = count($this->childNodes) - 1;
		for ($i = $last; $i > $pos; $i--) {
			$this->insert_in($this->childNodes[$i], $i -1);
		}
		unset ($this->childNodes[$last]);
		/*$this->triggerEvent('childRemoved', array (
			'child' => $old
		));*/
	}
	function insert_before(& $old, & $new) {
		$pos = $old->parentPosition;
		for ($i = count($this->childNodes); $i > $pos; $i--) {
			$this->insert_in($this->childNodes[$i -1], $i);
		}
		$this->insert_in($new, $pos);
	}
	function setAttribute($name, $val) {
		$this->attributes[$name] = $val;
		/*$this->triggerEvent('attributeSet', array (
			'attribute' => $name,
			'value' => $val
		));*/
	}
	function getAttribute($attribute) {
		return $this->attributes[$attribute];
	}
}
?>