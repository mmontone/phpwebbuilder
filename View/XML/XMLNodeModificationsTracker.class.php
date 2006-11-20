<?php

class XMLNodeModificationsTracker extends XMLNode {
	var $modifications;
	var $toFlush = null;
	function XMLNodeModificationsTracker($tag_name = null, $attributes = array ()) {
		$this->toFlush =& new WeakReference(new NullXMLNodeModification($this));
		parent :: XMLNode($tag_name, $attributes);
	}

	function & instantiate() {
		return $this;
	}
	function addChildMod(&$mod){
		if ($this->willFlush()) {
			$t =& $this->toFlush->getTarget();
			$t->addChildMod($mod);
		} else {
			$this->toFlush->setTarget($m=& new ChildModificationsXMLNodeModification($this));
			$m->addChildMod($mod);
			if ($this->parentNode) {
				$this->parentNode->addChildMod(&$m);
			}
		}
	}
	function flushModifications() {
		$n = null;
		$a = array();
		//$this->modifications =& $a;
		//$this->toFlush->setTarget(new NullXMLNodeModification($this));
		$cn =& $this->childNodes;
		foreach (array_keys($cn) as $key) {
			/*if (!is_object($cn[$key])) {
				print_backtrace($cn[$key]);
				//var_dump($cn[$key]);
			}*/
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
		$child->toFlush->setTarget(new AppendChildXMLNodeModification($this, $child));

		// Tag the child as an appended  node. See what happens when a "append" modification is found in AjaxPageRenderer
		return parent :: appendChild($child);
	}
	function willFlush(){
		$t =& $this->toFlush->getTarget();
		return $t!==null && getClass($t)!='nullxmlnodemodification' && getClass($t)!='childmodificationsxmlnodemodification';
	}
	function replaceChild(& $new_child, & $old_child) {
		// I don't want modifications on the $new_child to be taken into account by the page renderer
		$new_child->flushModifications();

		if ($old_child->willFlush()) {
			$tf =& $old_child->toFlush->getTarget();
			$tf->apply_replace($new_child);
			$new_child->toFlush->setTarget($tf);
		} else {
			$new_child->toFlush->setTarget(new ReplaceChildXMLNodeModification($new_child, $old_child, $this));
		}

		return parent :: replaceChild($new_child, $old_child);
	}

	function removeChild(& $child) {
		if ($child->willFlush()) {
			$tf =& $child->toFlush->getTarget();
			if (getClass($tf) == 'replacechildxmlnodemodification'){
				$old =& $tf->child;
				$this->addChildMod(new RemoveChildXMLNodeModification($this, $old));
			}
		}
		else {
			$mod =& new RemoveChildXMLNodeModification($this, $child);
			$this->modifications[$child->getId()] =& $mod;
		}
		return parent :: removeChild($child);
	}


	function redraw() {
		if($this->parentNode)	$this->parentNode->replaceChild($this, clone($this));
	}

	function setAttribute($attribute, $value) {
		$this->addChildMod(new SetAttributeXMLNodeModification($this, $attribute, $value));
		return parent :: setAttribute($attribute, $value);
	}

	function removeAttribute($attribute) {
		$this->addChildMod(new RemoveAttributeXMLNodeModification($this,$attribute));
		return parent::removeAttribute($attribute);
	}

	function insertBefore(&$old, &$new){
		$new->toFlush->setTarget(new InsertBeforeXMLNodeModification($this, $old, $new));
		return parent :: insertBefore($old, $new);
	}
	function renderAjaxResponseCommands(& $node) {
		if ($this->toFlush->isNotNull()) {
			$tf =& $this->toFlush->getTarget();
			$xml = $tf->renderAjaxResponseCommand();
		} else {
			foreach (array_keys($this->childNodes) as $i) {
				$xml .= $this->childNodes[$i]->renderAjaxResponseCommands();
			}
			return $xml;
		}
	}
}

/*
 * A node is asked to render itself
 * If it has no rendering, then do nothing.
 * Modifications are weakreferences
 *
 * When modifying a node, ask the parent to remember to show the child's changes (recursively).
 *
 * */

?>