<?php

class RadioButton extends Widget {
	//TODO Remove view
	function valueChanged(&$value_model, &$params) {
		if ($this->value_model->getValue()) {
			$this->view->setAttribute('checked','checked');
		} else{
			$this->view->removeAttribute('checked');
		}
	}
}

?>