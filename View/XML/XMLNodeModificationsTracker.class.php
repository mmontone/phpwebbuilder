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
	function addChildMod($pos,&$mod){
		if ($this->willFlush()) {
			$t =& $this->toFlush->getTarget();
			$t->addChildMod($pos,$mod);
		} else {
			$m=& new ChildModificationsXMLNodeModification($this);
			$this->toFlush->setTarget($m);
			$m->addChildMod($pos,$mod);
			if ($this->parentNode) {
				$this->parentNode->addChildMod($this->parentPosition,$this);
			}
		}
	}
	function removeChildMod($pos){
		$tf =& $this->toFlush->getTarget();
		$tf->removeChildMod($pos);
	}
	function flushModifications() {
		$n = null;
		$a = array();
		$this->modifications =& $a;
		$this->toFlush->setTarget(new NullXMLNodeModification($this));
		/*
		$cn =& $this->childNodes;
		foreach (array_keys($cn) as $key) {
			if (!is_object($cn[$key])) {
				print_backtrace($cn[$key]);
				//var_dump($cn[$key]);
			}
			$cn[$key]->flushModifications();
		}*/
	}

	function createElement($tag_name, & $controller) {
		$element = & new XMLNodeModificationsTracker($tag_name);
		$element->controller = & $controller;
		return $element;
	}

	function appendChild(& $child) {
		// I don't want modifications on the $child to be taken into account by the page renderer
		//$child->flushModifications();
		$child->toFlush->setTarget(new AppendChildXMLNodeModification($this, $child));
		$ret = parent :: appendChild($child);
		$this->addChildMod($child->parentPosition,$child);
		// Tag the child as an appended  node. See what happens when a "append" modification is found in AjaxPageRenderer
		return $ret;
	}
	function willFlush(){
		$t =& $this->toFlush->getTarget();
		return $t!==null && getClass($t)!='nullxmlnodemodification';
	}
	function willFlushNode(){
		$t =& $this->toFlush->getTarget();
		return $t!==null && getClass($t)!='nullxmlnodemodification' && getClass($t)!='childmodificationsxmlnodemodification';
	}
	function replaceChild(& $new_child, & $old_child) {
		// I don't want modifications on the $new_child to be taken into account by the page renderer
		//$new_child->flushModifications();
		if ($old_child->willFlushNode()) {
			$tf =& $old_child->toFlush->getTarget();
			$tf->apply_replace($new_child);
			$new_child->toFlush->setTarget($tf);
			$this->addChildMod($old_child->parentPosition,$new_child);
		} else {
			$new_child->toFlush->setTarget(new ReplaceChildXMLNodeModification($new_child, $old_child, $this));
			$this->addChildMod($old_child->parentPosition,$new_child);
		}
		return parent :: replaceChild($new_child, $old_child);
	}

	function removeChild(& $child) {
		if ($child->willFlushNode()) {
			$tf =& $child->toFlush->getTarget();
			if (getClass($tf) == 'replacechildxmlnodemodification'){
				$this->addChildMod($child->parentPosition,new RemoveChildXMLNodeModification($this, $child));
			} else {
				$this->removeChildMod($child->parentPosition);
			}
		}
		else {
			$mod =& new RemoveChildXMLNodeModification($this, $child);
			$this->addChildMod($child->parentPosition,$mod);
		}
		return parent :: removeChild($child);
	}


	function redraw() {
		if($this->parentNode)	$this->parentNode->replaceChild($this, clone($this));
	}

	function setAttribute($attribute, $value) {
		$this->addChildMod($attribute,new SetAttributeXMLNodeModification($this, $attribute, $value));
		return parent :: setAttribute($attribute, $value);
	}

	function removeAttribute($attribute) {
		$this->addChildMod($attribute,new RemoveAttributeXMLNodeModification($this,$attribute));
		return parent::removeAttribute($attribute);
	}

	function insertBefore(&$old, &$new){
		$new->toFlush->setTarget(new InsertBeforeXMLNodeModification($this, $old, $new));
		$ret = parent :: insertBefore($old, $new);
		$this->addChildMod($new->parentPosition,$new);
		return $ret;
	}
	function printTree(){
		$tg =& $this->toFlush->getTarget();
		return getClass($this) .'('.$this->getId().')'.  '{'.$tg->printTree().'}';
	}
	function renderAjaxResponseCommand() {
		if ($this->willFlush()) {
			$tf =& $this->toFlush->getTarget();
			$xml = $tf->renderAjaxResponseCommand();
		} else {
			$xml = '';
		}
		/*foreach (array_keys($this->childNodes) as $i) {
			$xml .= $this->childNodes[$i]->renderAjaxResponseCommand();
		}*/
		//$this->flushModifications();
		return $xml;
	}
}

?>