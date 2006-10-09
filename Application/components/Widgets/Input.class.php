<?php

class Input extends Widget {
	//TODO Remove view
	function valueChanged(&$value_model, &$params) {
		if ($this->view){
			$this->view->setAttribute('value', $this->printValue());
		}
	}
	function printValue(){
		return toAjax($this->value_model->getValue());
	}
}

?>