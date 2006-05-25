<?php

class CheckBox extends FormComponent {
    function CheckBox(&$boolHolder) {
    	parent::FormComponent($boolHolder);
    }

    function createNode(){
		parent::createNode();
		$in =& $this->view;
		$in->setTagName('input');
		$in->setAttribute('type','checkbox');
		$in->setAttribute('onchange','javascript:checkBoxChanged(this,enqueueUpdate)');
	}

	function valueChanged(&$value_model, &$params) {
		if ($this->getValue())
			$this->view->setAttribute('checked','checked');
		else
			$this->view->removeAttribute('checked');
	}

	function prepareToRender(){
		if ($this->getValue())
			$this->view->setAttribute('checked', 'checked');
	}

	function onChangeSend($selector, &$target) {
		$this->addEventListener(array('changed'=>$selector), $target);
		$this->view->setAttribute('onchange','javascript:checkBoxChanged(this,sendUpdate)');
	}

	/*
	function viewUpdated($params) {
		print_r($params);
		$value =& $this->value_model->getValue();
		echo $value;
		if ($params != $value){
			$oldval =  $value;
			echo "Setting";
			$this->value_model->primitiveSetValue($params);
			echo $this->value_model->getValue();
			$this->value_model->triggerEvent('changed', $oldval);
		}
	}*/
}
?>