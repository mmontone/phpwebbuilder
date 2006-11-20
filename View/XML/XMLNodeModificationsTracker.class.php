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
		//echo "<br/>putting a ".getClass($mod)." in '$pos'";
		/*if ($this->parentNode){
			echo "<br/>". $this->getId() .' '. getClass($mod);
			if ($mod->toFlush) echo "(".getClass($mod->toFlush->getTarget()).")";
			echo "(in ".getClass($this->toFlush->getTarget()).")";
		}*/
		if ($this->willFlush()) {
			$t =& $this->toFlush->getTarget();
			$t->addChildMod($pos,$mod);
		} else {
			$this->toFlush->setTarget($m=& new ChildModificationsXMLNodeModification($this));
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
		$ret = parent :: replaceChild($new_child, $old_child);
		if ($old_child->willFlushNode()) {
			$tf =& $old_child->toFlush->getTarget();
			$tf->apply_replace($new_child);
			$new_child->toFlush->setTarget($tf);
			$this->addChildMod($new_child->parentPosition,$new_child);
		} else {
			$new_child->toFlush->setTarget(new ReplaceChildXMLNodeModification($new_child, $old_child, $this));
			$this->addChildMod($new_child->parentPosition,$new_child);
		}
		return $ret;
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
		return parent :: removeChild($child);;
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