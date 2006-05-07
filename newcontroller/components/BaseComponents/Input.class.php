<?php

require_once dirname(__FILE__) . '/FormComponent.class.php';

class Input extends FormComponent{
	function Input (&$value_model){
		parent::FormComponent($value_model);
	}

	function createNode(){
		$in =& $this->view;
		$in->setTagName('input');
		$in->setAttribute('value', $this->value_model->getValue());
	}

	function valueChanged(&$value_model, &$params) {
		$this->view->setAttribute('value', $this->value_model->getValue());
	}
}

?>