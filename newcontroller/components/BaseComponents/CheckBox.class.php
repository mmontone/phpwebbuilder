<?php

class CheckBox extends FormComponent {
    function createNode(){
		$in =& $this->view;
		$in->setTagName('input');
		$in->setAttribute('type','checkbox');
		$in->setAttribute('onChange','javascript:checkBoxChanged(this,enqueueUpdate)');
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
		$this->view->setAttribute('onChange','javascript:checkBoxChanged(this,sendUpdate)');
	}
}
?>