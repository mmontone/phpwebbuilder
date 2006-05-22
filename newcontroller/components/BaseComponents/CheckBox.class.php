<?php

class CheckBox extends FormComponent {
    function createNode(){
		$in =& $this->view;
		$in->setTagName('input');
		$in->setAttribute('type','checkbox');
		$in->setAttribute('onChange','javascript:checkBoxChanged(this)');
	}

	function valueChanged(&$value_model, &$params) {
		if ($this->value_model->getValue())
			$this->view->setAttribute('checked','checked');
		else
			$this->view->removeAttribute('checked');
	}

	function prepareToRender(){
		if ($this->value_model->getValue())
			$this->view->setAttribute('checked', 'checked');
	}
}
?>