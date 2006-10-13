<?php

class RadioButtonHTMLHandler extends WidgetHTMLHandler{
	function prepareToRender() {
		parent::prepareToRender();
		$this->valueChanged($this->component->value_model, $n=null);
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