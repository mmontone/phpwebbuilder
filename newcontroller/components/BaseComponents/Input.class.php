<?php

require_once dirname(__FILE__) . '/FormComponent.class.php';

class Input extends FormComponent{
	function initializeView(&$view){
		$view->setTagName('input');
		$view->setAttribute('type', 'text');
	}
	function valueChanged(&$value_model, &$params) {
		if ($this->view){
			$this->view->setAttribute('value', $this->printValue());
		}
	}
	function prepareToRender(){
		$this->view->setAttribute('value', $this->printValue());
	}
	function printValue(){
		return toAjax($this->value_model->getValue());
	}

}

?>