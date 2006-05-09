<?php

require_once dirname(__FILE__) . '/FormComponent.class.php';

class TextAreaComponent extends FormComponent{
	function createNode(){
		$in =& $this->view;
		$in->setTagName('textarea');
	}
	function prepareToRender(){
		$this->view->append_child(new XMLTextNode($this->value_model->getValue()));
	}
	function valueChanged(&$value_model, &$params) {
		$this->view->replace_child($this->view->first_child(), $this->value_model->getValue());
	}
}

?>