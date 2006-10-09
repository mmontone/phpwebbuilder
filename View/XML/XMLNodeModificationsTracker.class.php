<?php

class XMLNodeModificationsTracker extends XMLNode {
	var $modifications;
	var $toFlush = null;
	function XMLNodeModificationsTracker($tag_name = 'div', $attributes = array ()) {
		parent :: XMLNode($tag_name, $attributes);
		$this->modifications = array();
		$this->toFlush =& new NullXMLNodeModification($this);
	}

	function & instantiate() {
		return $this;
	}

	function flushModifications() {
		$n = null;
		$a = array();
		$this->modifications =& $a;
		$this->toFlush =& new NullXMLNodeModification($this);
		$cn =& $this->childNodes;
		foreach (array_keys($cn) as $key) {
			if (!is_object($cn[$key])) {
				print_backtrace($cn[$key]);
				//var_dump($cn[$key]);
			}
			$cn[$key]->flushModifications();
		}
	}

	function createElement($tag_name, & $controller) {
		$element = & new XMLNodeModificationsTracker($tag_name);
		$element->controller = & $controller;
		return $element;
	}

	function appendChild(& $child) {
		// I don't want modifications on the $child to be taken into account by the page renderer
		$child->flushModifications();
		$child->toFlush =& new AppendChildXMLNodeModification($this, $child);
		// Tag the child as an appended  node. See what happens when a "append" modification is found in AjaxPageRenderer
		return parent :: appendChild($child);
	}

	function replaceChild(& $new_child, & $old_child) {
		// I don't want modifications on the $new_child to be taken into account by the page renderer
		$new_child->flushModifications();

		if ($old_child->toFlush->willFlush()) {
			$old_child->toFlush->apply_replace($new_child);
			$new_child->toFlush =& $old_child->toFlush;
		} else {
			$new_child->toFlush = & new ReplaceChildXMLNodeModification($new_child, $old_child, $this);
		}

		return parent :: replaceChild($new_child, $old_child);
	}

	function removeChild(& $child) {
		if ($child->toFlush->willFlush()) {
			if (getClass($child->toFlush) == "replacechildxmlnodemodification"){
				$old =& $child->toFlush->child;
				$this->modifications[$old->getId()] =& new RemoveChildXMLNodeModification($this, $old);
			}
		}
		else {
			$mod =& new RemoveChildXMLNodeModification($this, $child);
			$this->modifications[$child->getId()] =& $mod;
		}
		return parent :: removeChild($child);
	}


	function redraw() {
		$this->parentNode->replaceChild($this, clone($this));
	}

	function setAttribute($attribute, $value) {
		$this->modifications[$attribute] = & new SetAttributeXMLNodeModification($this, $attribute, $value);
		return parent :: setAttribute($attribute, $value);
	}

	function removeAttribute($attribute) {
		$this->modifications[$attribute] =& new RemoveAttributeXMLNodeModification($this,$attribute);
		return parent::removeAttribute($attribute);
	}

	function insertBefore(&$old, &$new){
		$new->toFlush = & new InsertBeforeXMLNodeModification($this, $old, $new);
		return parent :: insertBefore($old, $new);
	}

	function printString() {
		//$this->getRealId();
		$attrs = "";
		foreach ($this->attributes as $name => $val) {
			$attrs .= ' ' . $name . '="' . $val . '"';
		}
		//$mods = $this->printModifications();

		if (count($this->childNodes) == 0) {
			$ret = "&lt;printing $this->tagName $attrs>";
			//$ret = "\n   &lt;modifications>";
//			$ret .= "\n      $mods";
			//$ret .= "\n   &lt;/modifications>\n";
			$ret .="&lt;/$this->tagName>";
		}
		else {
			$childs = '';
			$ks = array_keys($this->childNodes);
			foreach ($ks as $k) {
				$childs .= $this->childNodes[$k]->printString();
			}
			$childs = str_replace("\n", "\n   ", $childs);
			$ret = "\n&lt;printing $this->tagName $attrs>&lt;children>$childs&lt;/children>\n";
			//$ret .= "\n   &lt;modifications>";
//			$ret .= "\n      $mods";
			//$ret .= "\n   &lt;/modifications>\n";
			$ret .= "&lt;/$this->tagName>\n";
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