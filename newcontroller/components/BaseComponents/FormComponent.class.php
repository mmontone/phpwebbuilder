<?php

require_once dirname(__FILE__) . '/../../Component.class.php';

class FormComponent extends Component
{
	var $value_model;

	function FormComponent(&$value_model, $callback_actions=array('on_accept' => 'notification_accepted')) {
		parent::Component($callback_actions);
		if ($value_model==null) {
			$this->value_model =& new ValueHolder($null = null);
		} else {
			$this->value_model =& $value_model;
		}
		$this->value_model->onChangeSend('valueChanged', $this);
	}

	function declare_actions(){return array();}

	function viewUpdated($params) {
		$value =& $this->value_model->getValue();
		if ($params != $value){
			$oldval =  $value;
			$this->value_model->primitiveSetValue($params);
			$this->triggerEvent('changed', $oldval);
		}
	}
	function setValue(&$params) {
		$this->value_model->setValue($params);
	}

	function &createDefaultView(){
		$this->view =& parent::createDefaultView();
		$this->createNode();
		return $this->view;
	}

	function createNode(){}
}

?>