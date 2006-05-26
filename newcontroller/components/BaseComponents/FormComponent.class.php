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

	function setEvents(&$view, $class, $strategy) {
		/* Default events, override in subclasses */
		$this->setOnChangeEvent($view, $class, $strategy);
		$this->setOnBlurEvent($view, $class, $strategy);
		$this->setOnFocusEvent($view, $class, $strategy);
	}

	function setOnChangeEvent(&$view, $class, $updateStrategy) {
		$view->setAttribute('onchange',"javascript:componentChanged(this,{$class}GetValue,$updateStrategy)");
	}

	function setOnBlurEvent(&$view, $class, $updateStrategy) {
		$view->setAttribute('onblur',"javascript:componentBlur(this,{$class}GetValue,$updateStrategy)");
	}

	function setOnFocusEvent(&$view, $class, $updateStrategy) {
		$view->setAttribute('onfocus',"javascript:componentFocus(this,{$class}GetValue,$updateStrategy)");
	}

	function valueChanged(){}
	function viewUpdated($params) {
		if (preg_match('/_ui_event_((?:.)*)/',$params, $event)) {
			$event = $event[1];
			$this->triggerEvent($event);
		}
		else {
			$value =& $this->value_model->getValue();
			if ($params != $value){
				$oldval =  $value;
				$this->value_model->primitiveSetValue($params);
				$this->value_model->triggerEvent('changed', $oldval);
			}
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
		$this->initializeView($this->view);
		$this->setEvents($this->view, get_class($this), 'enqueueUpdates');

		return $this->view;
	}

	function onChangeSend($selector, &$target) {
		$this->addEventListener(array('changed'=>$selector), $target);
		$this->setOnChangeEvent($this->view, get_class($this), 'sendUpdate');
	}

	function onFocusSend($selector, &$target) {
		$this->addEventListener(array('focus'=>$selector), $target);
		$this->setOnFocusEvent($this->view, get_class($this), 'sendUpdate');
	}

	function onBlurSend($selector, &$target) {
		$this->addEventListener(array('blur'=>$selector), $target);
		$this->setOnBlurEvent($this->view, get_class($this), 'sendUpdate');
	}
}

?>