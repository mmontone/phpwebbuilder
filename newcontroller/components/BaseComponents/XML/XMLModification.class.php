<?php

class XMLNodeModification {
	var $target;

	function XMLNodeModification(&$target) {
		$this->target =& $target;
	}

	function visit(&$visitor, $params) {
		$visit_selector = 'visit' . get_class($this);
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
		//assert($this->target->fullPath!="");
		$xml = '<replace_node path="' . $target->fullPath . '">';
		$xml .= $this->replacement->render();
		$xml .= '</replace_node>';
		return $xml;
	}

	// TODO: make $target instance variable
	function printString() {
		$ret = '<replace_node path="' . $this->target->fullPath . '">';
		$ret .= "\n   ";
		$ret .= $this->replacement->printString();
		$ret .= "\n";
		$ret .= '</replace_node>';
		return $ret;

	}
}

class ReplaceChildXMLNodeModification extends XMLNodeModification {
	var $child;
	var $replacement;

	function ReplaceChildXMLNodeModification(& $replacement, & $child, &$target) {
		parent::XMLNodeModification($target);
		$this->child = & $child;
		$this->replacement = & $replacement;
	}

	function renderAjaxResponseCommand() {
		//assert($this->child->fullPath!="");
		$xml = '<replace_node path="' . $this->child->fullPath . '">';
		$xml .= $this->replacement->render();
		$xml .= '</replace_node>';
		return $xml;
	}

	function printString() {
		$ret = '<replace_node path="' . $this->child->fullPath . '">';
		$ret .= "\n   ";
		$ret .= $this->replacement->printString();
		$ret .= "\n";
		$ret .= '</replace_node>';
		return $ret;
	}

}

class AppendChildXMLNodeModification extends XMLNodeModification {
	var $child;

	function AppendChildXMLNodeModification(&$target, & $child) {
		parent::XMLNodeModification($target);
		$this->child = & $child;
	}

	function renderAjaxResponseCommand() {
		//assert($this->target->fullPath);
		$xml = '<append_child path="' . $this->target->fullPath . '">';
		$xml .= $this->child->render();
		$xml .= '</append_child>';
		return $xml;
	}

	function printString() {
		$ret = '<append_child path="' . $this->target->fullPath . '">';
		$ret .= "\n   ";
		$ret .= $this->child->printString();
		$ret .= "\n";
		$ret .= '</append_child>';
		return $ret;

	}

}

class RemoveNodeXMLNodeModification extends XMLNodeModification {
	function RemoveNodeXMLNodeModification(&$target)  {
		parent::XMLNodeModification($target);
	}

	function renderAjaxResponseCommand() {
		//assert($this->target->fullPath!="");
		$xml = '<remove_node path="' . $this->target->fullPath . '" />';
		return $xml;
	}

	function printString() {
		$ret = '<remove_node path="' . $this->target->fullPath . '"/>';
		return $ret;
	}

}

class RemoveChildXMLNodeModification extends XMLNodeModification {
	var $child;

	function RemoveChildXMLNodeModification(&$target, & $child) {
		parent::XMLNodeModification(&$target);
		$this->child = & $child;
	}

	function renderAjaxResponseCommand(&$target) {
		assert($this->child->fullPath!="");
		$xml = '<remove_node path="' . $this->child->fullPath . '" />';
		return $xml;
	}

	function printString() {
		$ret = '<remove_node path="' . $target->child->fullPath . '"/>';
		return $ret;
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
		assert($this->target->fullPath!="");
		$xml = '<set_attribute path="' . $this->target->fullPath . '">';
		$xml .= '<attribute>' . $this->attribute . '</attribute>';
		$xml .= '<value> ' . $this->value . '</value>';
		$xml .= '</set_attribute>';
		return $xml;
	}

	function printString() {
		$ret = '<set_attribute path="' . $this->target->fullPath . '">';
		$ret .= "\n   ";
		$ret .= '<attribute>' . $this->attribute . '</attribute>';
		$ret .= "\n   ";
		$ret.= '<value> ' . $this->value . '</value>';
		$ret .= "\n";
		$ret .= '</set_attribute>';
		return $ret;
	}
}

?>