<?php
class DOMXMLNode extends PWBObject {
	var $childNodes = array ();
	var $parentNode = null;
	var $tagName;
	var $attributes;
	var $parentPosition = null;
	var $nextNode;

	function DOMXMLNode($tag_name = 'div', $attributes = array ()) {
		$this->tagName = $tag_name;
		$this->attributes = $attributes;
		$this->nextNode = 0;
	}

	function release() {
		parent :: release();
		$n = null;
		$this->parentNode =& $n;
		$this->parentPosition =& $n;
	}

	function & createElement($tag_name) {
		$class_name = getClass($this);
		$element = & new $class_name ($tag_name);
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

	function & first_child() {
		$ks = array_keys($this->childNodes);
		return $this->childNodes[$ks[0]];
	}
	function & last_child() {
		$ks = array_keys($this->childNodes);
		$p = count($ks)-1;
		if (isset($ks[$p])){
			return $this->childNodes[$ks[$p]];
		} else {
			$v = null;
			return $v;
		}
	}
	function insert_in(& $xml, $position) {
		$this->childNodes[$position] = & $xml;
		$xml->parentNode = & $this;
		$xml->parentPosition = $position;
	}

	function appendChild(& $xml) {
		$this->insert_in($xml, $this->nextNode++);
	}
	function replaceChild(& $new, & $old) {
		$this->insert_in($new, $old->parentPosition);
		$old->release();
	}

	function removeChild(& $old) {
		$pos = $old->parentPosition;

		if (!isset ($this->childNodes[$pos])) {
			print_backtrace('Error removing child');
			echo $this->printString();
			exit;
		}

		unset ($this->childNodes[$pos]);
		$old->release();
	}

	function removeChilds() {
		foreach(array_keys($this->childNodes) as $c) {
			$child =& $this->childNodes[$c];
			$child->release();
		}

		$temp = array ();
		$this->childNodes = & $temp;
	}

	function insertBefore(& $old, & $new) {
		$pos = $old->parentPosition;
		$ks = array_keys($this->childNodes);
		$c = count($ks);
		$last = $c -1;
		$lastElem = & $this->childNodes[$ks[$last]];
		$nn = $this->nextNode++;
		$this->childNodes[$nn] = & $lastElem;
		$lastElem->parentPosition = $nn;
		for ($i = $last; $ks[$i] != $pos; $i--) {
			$this->insert_in($this->childNodes[$ks[$i -1]], $ks[$i]);
		}
		$this->insert_in($new, $pos);
	}

	/*
		function checkConsistency() {
			foreach(array_keys($this->childNodes) as $i) {
				if ($this->childNodes[$i]->parentPosition != $i) {
					print_backtrace('Children inconsitency');
					exit;
				}
			}
		}*/

	function setAttribute($name, $val) {
		$this->attributes[$name] = $val;
	}
	function getAttribute($attribute) {
		if (isset ($this->attributes[$attribute])) {
			return $this->attributes[$attribute];
		}
		else {
			return '';
		}
	}

	function removeAttribute($attr) {
		unset ($this->attributes[$attr]);
	}
}
?>