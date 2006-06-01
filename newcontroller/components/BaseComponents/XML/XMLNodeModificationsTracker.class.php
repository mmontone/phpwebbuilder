<?php
require_once dirname(__FILE__) . '/XMLNode.class.php';

class XMLNodeModificationsTracker extends XMLNode {
	var $modifications;
	var $toFlush = null;
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
		$n = null;
		$this->toFlush =& $n;
		$cn =& $this->childNodes;
		foreach (array_keys($cn) as $key) {
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
		$child->modifications[] =& $child->toFlush;
		return parent :: appendChild($child);
	}

	function replaceChild(& $new_child, & $old_child) {
		// I don't want modifications on the $new_child to be taken into account by the page renderer
		$new_child->flushModifications();
		if ($old_child->toFlush) {
			$old_child->toFlush->apply_replace($new_child);
			$new_child->toFlush =& $old_child->toFlush;
			$new_child->modifications[] =& $new_child->toFlush;
			$n=null;
			$old_child->toFlush =& $n;
		} else {
			// Tag the new_child as a replaced node. See what happens when a "replace" modification is found in AjaxPageRenderer
			$new_child->toFlush = & new ReplaceChildXMLNodeModification($new_child, $old_child, $this);
			$new_child->modifications[] =& $new_child->toFlush;
		}
		return parent :: replaceChild($new_child, $old_child);
	}

	function removeChild(& $child) {
		if ($child->toFlush) {
			if (get_class($child->toFlush) == "replacechildxmlnodemodification"){
				$old =& $child->toFlush->child;
				$old->toFlush = & new RemoveChildXMLNodeModification($this, $old);
				$this->modifications[] = & $old->toFlush;
			}
		} else {
			$child->toFlush = & new RemoveChildXMLNodeModification($this, $child);
			$this->modifications[] = & $child->toFlush;
		}
		return parent :: removeChild($child);
	}
	function redraw(){
		$this->parentNode->replaceChild($this, $new_xml = $this);
	}
	function setAttribute($attribute, $value) {
		$this->modifications[] = & new SetAttributeXMLNodeModification($this, $attribute, $value);
		return parent :: setAttribute($attribute, $value);
	}

	function removeAttribute($attribute) {
		$this->modifications[] =& new RemoveAttributeXMLNodeModification($this,$attribute);
		return parent::removeAttribute($attribute);
	}

	function insertBefore(&$old, &$new){
		$new->toFlush = & new InsertBeforeXMLNodeModification($this, $old, $new);
		$this->modifications[] = & $new->toFlush;
		return parent :: insertBefore($old, $new);
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