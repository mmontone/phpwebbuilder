<?php

class RadioButtonHTMLHandler extends WidgetHTMLHandler{
	function prepareToRender() {
		parent::prepareToRender();
		$this->valueChanged($this->component->value_model, $n=null);
	}
    function initializeView(&$view){
		$view->setTagName('input');
		$view->setAttribute('type','radio');
		$view->setAttribute('name',$this->component->rg->name);
		$view->setAttribute('value',$this->component->value);
	}
	function valueChanged(&$value_model, &$params) {
		if ($this->component->getValue()) {
			$this->view->setAttribute('checked','checked');
		} else{
			$this->view->removeAttribute('checked');
		}
	}

}
?>