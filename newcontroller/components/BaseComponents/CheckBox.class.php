<?php

class CheckBox extends FormComponent {
    function createNode(){
		parent::createNode();
		$in =& $this->view;
		$in->setTagName('input');
		$in->setAttribute('type','checkbox');
		$in->setAttribute('onchange','javascript:checkBoxChanged(this,enqueueUpdate)');
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

	function onChangeSend($selector, &$target) {
		$this->addEventListener(array('changed'=>$selector), $target);
		$this->view->setAttribute('onchange','javascript:checkBoxChanged(this,sendUpdate)');
	}
}
?>