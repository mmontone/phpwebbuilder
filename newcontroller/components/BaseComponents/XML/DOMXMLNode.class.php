<?php
class DOMXMLNode //extends PWBObject
{
	var $childNodes = array ();
	var $parentNode = null;
	var $tagName = "div";
	var $attributes = array ();
	var $parentPosition = null;
	var $nextNode = 0;
	//var $fullPath = '';

	function DOMXMLNode($tag_name = "div", $attributes = array ()) {
		$this->tagName = $tag_name;
		$this->attributes = $attributes;
		$this->nextNode = 0;
		$this->fullPath = '';
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
		$ks = array_keys($this->childNodes);
		return $this->childNodes[$ks[0]];
	}

	function insert_in(& $xml, $position) {
		$this->childNodes[$position] = & $xml;
		$xml->parentNode = & $this;
		$xml->parentPosition = $position;
	}

	function append_child(& $xml) {
		$this->insert_in($xml,$this->nextNode++);
	}
	function replace_child(& $new, & $old) {
		$this->insert_in($new, $old->parentPosition);
		$n = null;
		$old->parentNode = & $n;
		$old->parentPosition = & $n;
	}

	function remove_child(& $old) {
		$last = $old->parentPosition;
		$null = null;
		$this->childNodes[$last] =& $null;
		$old->parentNode = & $null;
		$old->parentPosition = & $null;
		unset($this->childNodes[$last]);

	}
	function remove_childs(){
		$temp = array();
		$this->childNodes =& $temp;
	}
	function insert_before(& $old, & $new) {
		$pos = $old->parentPosition;
		$ks = array_keys($this->childNodes);
		$c = count($ks);
		$i=$c-1;
		$lastElem =& $this->childNodes[$ks[$i]];
		$nn = $this->nextNode++;
		$this->childNodes[$nn]=&$lastElem;
		$lastElem->parentPosition=$nn;
		for (; $ks[$i] != $pos; $i--) {
			$this->insert_in($this->childNodes[$ks[$i-1]], $ks[$i]);
		}
		$this->insert_in($new, $pos);
	}

	function setAttribute($name, $val) {
		$this->attributes[$name] = $val;
	}
	function getAttribute($attribute) {
		return $this->attributes[$attribute];
	}
}
?>