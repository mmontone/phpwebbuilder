<?php

class XMLNodeModification {
	var $target;

	function XMLNodeModification(&$target) {
		$this->target =& $target;
	}

	function visit(&$visitor, $params) {
		$visit_selector = 'visit' . getClass($this);
		return $visitor->$visit_selector($this, $params);
	}
}

class ReplaceNodeXMLNodeModification extends XMLNodeModification {
	var $replacement;

	function ReplaceNodeXMLNodeModification(& $replacement, &$target) {
		parent::XMLNodeModification(&$target);
		$this->replacement = & $replacement;
	}
	function renderAjaxResponseCommand(&$target) {
		$xml = '<repn id="' . $target->getId() . '">';
		$xml .= $this->replacement->render();
		$xml .= '</repn>';
		return $xml;
	}
}

class ReplaceChildXMLNodeModification extends XMLNodeModification {
	var $child;
	var $replacement;
	function ReplaceChildXMLNodeModification(& $replacement, & $child, &$target) {
		parent::XMLNodeModification($target);
		$this->child = & $child;
		$this->childId = $child->getId();
		$this->replacement = & $replacement;
	}
	function renderAjaxResponseCommand() {
		$xml = '<repn id="' . $this->childId . '">';
		$xml .= $this->replacement->render();
		$xml .= '</repn>';
		return $xml;
	}
	function apply_replace(&$elem){
		$this->replacement = & $elem;
	}
}

class InsertBeforeXMLNodeModification extends XMLNodeModification {
	var $old;
	var $new;
	function InsertBeforeXMLNodeModification(&$target, &$old, &$new){
		parent::XMLNodeModification($target);
		$this->old = & $old;
		$this->new = & $new;
	}
	function renderAjaxResponseCommand() {
		$xml = '<insert id="' . $this->old->getId() . '">';
		$xml .= $this->new->render();
		$xml .= '</insert>';
		return $xml;
	}
	function apply_replace(&$elem){
		$this->new = & $elem;
	}
}

class AppendChildXMLNodeModification extends XMLNodeModification {
	var $child;
	function AppendChildXMLNodeModification(&$target, & $child) {
		parent::XMLNodeModification($target);
		$this->child = & $child;
	}
	function renderAjaxResponseCommand() {
		$xml = '<append id="' . $this->target->getId() . '">';
		$xml .= $this->child->render();
		$xml .= '</append>';
		return $xml;
	}
	function apply_replace(&$elem){
		$this->child = & $elem;
	}
}

class RemoveNodeXMLNodeModification extends XMLNodeModification {
	function RemoveNodeXMLNodeModification(&$target)  {
		parent::XMLNodeModification($target);
	}
	function renderAjaxResponseCommand() {
		$xml = '<rem id="' . $this->target->getId() . '" />';
		return $xml;
	}
}

class RemoveChildXMLNodeModification extends XMLNodeModification {
	var $child;
	function RemoveChildXMLNodeModification(&$target, & $child) {
		parent::XMLNodeModification(&$target);
		$this->child = & $child;
	}
	function renderAjaxResponseCommand() {
		$xml = '<rem id="' . $this->child->getId() . '" />';
		return $xml;
	}
}

class SetAttributeXMLNodeModification extends XMLNodeModification {
	var $attribute;
	var $value;
	function SetAttributeXMLNodeModification(&$target, $attribute, $value) {
		parent::XMLNodeModification(&$target);
		$this->attribute = $attribute;
		$this->value = $value;
	}
	function renderAjaxResponseCommand() {
		$ret = '<setatt id="' . $this->target->getId() . '">';
		$ret.= '<att>' . $this->attribute . '</att>';
		$ret.= '<val> ' . $this->value . '</val>';
		$ret.= '</setatt>';
		return $ret;
	}
}

class RemoveAttributeXMLNodeModification extends XMLNodeModification {
	var $attribute;
	function RemoveAttributeXMLNodeModification(&$target, $attribute) {
		parent::XMLNodeModification(&$target);
		$this->attribute = $attribute;
	}
	function renderAjaxResponseCommand() {
		$xml = '<rematt id="' . $this->target->getId() . '">';
		$xml .= '<att>' . $this->attribute . '</att>';
		$xml .= '</rematt>';
		return $xml;
	}
}
?>