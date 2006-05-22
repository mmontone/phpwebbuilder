<?php

require_once dirname(__FILE__) . '/FormComponent.class.php';

class Input extends FormComponent{
	function createNode(){
		$in =& $this->view;
		$in->setTagName('input');
	}
	function valueChanged(&$value_model, &$params) {
		if ($this->view){
			$this->view->setAttribute('value', $this->value_model->getValue());
		}
	}
	function prepareToRender(){
		$this->view->setAttribute('value', $this->value_model->getValue());
	}
}

?>