<?php


// We access nodes by id (getRealId) at the moment.
// See how to access without id?

class XMLNodeModification {
	function visit(&$visitor, $params) {
		$visit_selector = 'visit' . get_class($this);
		return $visitor->$visit_selector($this, $params);
	}
}

class ReplaceNodeXMLNodeModification extends XMLNodeModification {
	var $replacement;

	function ReplaceNodeXMLNodeModification(& $replacement) {
		$this->replacement = & $replacement;
	}

	function renderAjaxResponseCommand(&$target) {
		$xml = '<replace_node path="' . $target->fullPath . '">';
		$xml .= $this->replacement->render();
		$xml .= '</replace_node>';
		return $xml;
	}
}

class ReplaceChildXMLNodeModification extends XMLNodeModification {
	var $child;
	var $replacement;

	function ReplaceChildXMLNodeModification(& $child, & $replacement) {
		$this->child = & $child;
		$this->replacement = & $replacement;
	}

	function renderAjaxResponseCommand(&$target) {
		$xml = '<replace_node path="' . $this->child->fullPath . '">';
		$xml .= $this->replacement->render();
		$xml .= '</replace_node>';
		return $xml;
	}
}

class AppendChildXMLNodeModification extends XMLNodeModification {
	var $child;

	function AppendChildXMLNodeModification(& $child) {
		$this->child = & $child;
	}

	function renderAjaxResponseCommand(&$target) {
		$xml = '<append_child path="' . $target->fullPath . '">';
		$xml .= $this->child->render();
		$xml .= '</append_child>';
		return $xml;
	}
}

class RemoveNodeXMLNodeModification extends XMLNodeModification {
	function renderAjaxResponseCommand(&$target) {
		$xml = '<remove_node path="' . $target->fullPath . '" />';
		return $xml;
	}
}

class RemoveChildXMLNodeModification extends XMLNodeModification {
	var $child;

	function RemoveChildXMLNodeModification(& $child) {
		$this->child = & $child;
	}

	function renderAjaxResponseCommand(&$target) {
		$xml = '<remove_node path="' . $this->child->fullPath . '" />';
		return $xml;
	}
}

class SetAttributeXMLNodeModification extends XMLNodeModification {
	var $attribute;
	var $value;

	function SetAttributeXMLNodeModification($attribute, $value) {
		$this->attribute = $attribute;
		$this->value = $value;
	}

	function renderAjaxResponseCommand(&$target) {
		$xml = '<set_attribute id="' . $target->getAttribute('id') . '">';
		$xml .= '<attribute>' . $this->attribute . '</attribute>';
		$xml .= '<value> ' . $this->value . '</value>';
		$xml .= '</set_attribute>';
		return $xml;
	}
}
?>