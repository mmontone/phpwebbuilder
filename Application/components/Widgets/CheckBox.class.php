<?php
class CheckBox extends Widget {
	var $disabled = false;

	function CheckBox(& $boolHolder) {
		parent :: Widget($boolHolder);
	}

	//TODO Remove view
	function valueChanged(& $value_model, & $params) {
			if ($this->getValue()) {
				$this->view->setAttribute('checked', 'checked');
			}
			else {
				if ($this->view->getAttribute('checked') == 'checked') {
					$this->view->removeAttribute('checked');
				}
			}
	}

	function valueFromForm(& $params) {
		return $params == '1';
	}
}
 ?>