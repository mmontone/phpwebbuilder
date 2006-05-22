<?php

require_once dirname(__FILE__) . '/../../Component.class.php';

class FormComponent extends Component
{
	var $value_model;

	function FormComponent(&$value_model, $callback_actions=array()) {
		if ($value_model==null) {
			$this->value_model =& new ValueHolder($null = null);
		} else {
			$this->value_model =& $value_model;
		}
		$this->value_model->onChangeSend('valueChanged', $this);
		parent::Component($callback_actions);
	}

	function createNode() {
		$this->view->setAttribute('onchange','javascript:checkBoxChanged(this,enqueueUpdate)');
	}

	function valueChanged(){}
	function viewUpdated($params) {
		$value =& $this->value_model->getValue();
		if ($params != $value){
			$oldval =  $value;
			$this->value_model->primitiveSetValue($params);
			$this->value_model->triggerEvent('changed', $oldval);
		}
	}
	function setValue($params) {
		$this->value_model->setValue($params);
	}
	function getValue() {
		return $this->value_model->getValue();
	}
	function &createDefaultView(){
		$this->view =& parent::createDefaultView();
		$this->createNode();
		return $this->view;
	}

	function onChangeSend($selector, &$target) {
		$this->addEventListener(array('changed'=>$selector), $target);
		$this->view->setAttribute('onchange','javascript:componentChanged(this,sendUpdate)');
	}
}

?>