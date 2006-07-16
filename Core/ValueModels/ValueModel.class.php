<?php
class ValueModel extends PWBObject {
	function setValue(& $value) {
		$old_value =& $this->getValue();
		$this->primitiveSetValue($value);
		$params = array();
		$params['value'] =& $this->getValue();
		$params['old_value'] =& $old_value;
		$this->triggerEvent('changed', $params);
	}

	function onChangeSend($call_back_selector, & $listener) {
		$this->addEventListener(array (
			'changed' => $call_back_selector
		), $listener);
	}
}
?>