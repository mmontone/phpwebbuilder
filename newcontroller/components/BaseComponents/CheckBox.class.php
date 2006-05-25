<?php
class CheckBox extends FormComponent {
	var $disabled = false;

	function CheckBox(& $boolHolder) {
		parent :: FormComponent($boolHolder);
	}

	function createNode() {
		parent :: createNode();
		$in = & $this->view;
		$in->setTagName('input');
		$in->setAttribute('type', 'checkbox');
		$in->setAttribute('onchange', 'javascript:checkBoxChanged(this,enqueueUpdate)');
	}

	function disable() {
		$this->disabled = true;
	}

	function enable() {
		$this->disabled = false;
	}

	function valueChanged(& $value_model, & $params) {
		if ($this->getValue())
			$this->view->setAttribute('checked', 'checked');
		else
			$this->view->removeAttribute('checked');
	}

	function prepareToRender() {
		if ($this->disabled)
			$this->view->setAttribute('disabled','disabled');

		if ($this->getValue())
			$this->view->setAttribute('checked', 'checked');
	}

	function onChangeSend($selector, & $target) {
		$this->addEventListener(array (
			'changed' => $selector
		), $target);
		$this->view->setAttribute('onchange', 'javascript:checkBoxChanged(this,sendUpdate)');
	}

	function viewUpdated($params) {
		$value = & $this->getValue();

		if ($params == 'on')
			$params = 1;
		else
			$params = 0;

		if ($params != $value) {
			$oldval = & $value;
			$this->value_model->primitiveSetValue($params);
			$this->value_model->triggerEvent('changed', $oldval);
		}
	}
}
?>