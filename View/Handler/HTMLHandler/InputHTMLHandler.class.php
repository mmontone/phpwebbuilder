<?php

class InputHTMLHandler extends WidgetHTMLHandler{
	function prepareToRender(){
		$this->view->setAttribute('value', $this->component->printValue());
	}
	function initializeDefaultView(&$view){
		$view->setTagName('input');
		$view->setAttribute('type', 'text');
	}
	function valueChanged(&$value_model, &$params) {
		if ($this->view){
			$this->view->setAttribute('value', $this->component->printValue());
		}
	}
}
?>