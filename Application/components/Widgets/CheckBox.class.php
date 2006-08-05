<?php
class CheckBox extends Widget {
	var $disabled = false;

	function CheckBox(& $boolHolder) {
		parent :: Widget($boolHolder);
	}

	function initializeDefaultView(&$view) {
		$view->setTagName('input');
		$view->setAttribute('type', 'checkbox');
	}

	function valueChanged(& $value_model, & $params) {
			if ($this->getValue()) {
				$this->view->setAttribute('checked', 'checked');
			}
			else {
				$this->view->removeAttribute('checked');
			}
	}

	function prepareToRender() {
		parent::prepareToRender();
		if ($this->getValue())
			$this->view->setAttribute('checked', 'checked');
	}

	function valueFromForm(& $params) {
		return $params == '1';
	}
}
 ?>