<?php
class CheckBox extends FormComponent {
	var $disabled = false;

	function CheckBox(& $boolHolder) {
		parent :: FormComponent($boolHolder);
	}

	function initializeView(&$view) {
		$view->setTagName('input');
		$view->setAttribute('type', 'checkbox');
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

	function &valueFromForm(&$params) {
		if ($params == 'on')
			return 1;
		else
			return 0;
	}
}
 ?>