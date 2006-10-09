<?php

class RadioButtonHTMLHandler extends WidgetHTMLHandler{
	function prepareToRender(){
		if ($this->component->value_model->getValue()) {
			$this->view->setAttribute('checked', 'checked');
		}
	}
    function initializeView(&$view){
		$view->setTagName('input');
		$view->setAttribute('type','radio');
	}
	function valueChanged(&$value_model, &$params) {
		if ($this->component->value_model->getValue()) {
			$this->view->setAttribute('checked','checked');
		} else{
			$this->view->removeAttribute('checked');
		}
	}

}
?>