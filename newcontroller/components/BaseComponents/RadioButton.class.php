<?php

class RadioButton extends Widget {
    function initializeView(&$view){
		$view->setTagName('input');
		$view->setAttribute('type','radio');
	}

	function valueChanged(&$value_model, &$params) {
		if ($this->value_model->getValue())
			$this->view->setAttribute('checked','checked');
		else
			$this->view->removeAttribute('checked');
		//$this->view->setAttribute('value', $this->value_model->getValue());
	}

	function prepareToRender(){
		if ($this->value_model->getValue())
			$this->view->setAttribute('checked', 'checked');

		//	$this->view->setAttribute('value', $this->value_model->getValue());
	}
}

?>