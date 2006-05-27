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

	function setEvents(&$view) {
		/* Default events, override in subclasses */
		$class = get_class($this);
		$view->setAttribute('onchange',"javascript:enqueueChange(this,{$class}GetValue)");
	}

	function setOnChangeEvent(&$view) {
		$class = get_class($this);
		$view->setAttribute('onchange',"javascript:componentChange(this,{$class}GetValue)");
	}

	function setOnBlurEvent(&$view) {
		$view->setAttribute('onblur',"javascript:componentBlur(this)");
	}

	function setOnFocusEvent(&$view) {
		$view->setAttribute('onfocus',"javascript:componentFocus(this)");
	}

	function valueChanged(){}
	function viewUpdated($params) {
		if (preg_match('/_ui_event_((?:.)*)/',$params, $event)) {
			$event = $event[1];
			$this->triggerEvent($event, $this);
		}
		else
			$this->updateValue($params);
	}

	function updateValue(&$params) {
		$new_value =& $this->valueFromForm($params);
		$value =& $this->getValue();

		if ($new_value != $value)
			$this->setValue($new_value);
	}

	function valueFromForm(&$params) {
		return $params;
	}

	function setValue($value) {
		$this->value_model->setValue($value);
	}

	function getValue() {
		return $this->value_model->getValue();
	}
	function &createDefaultView(){
		$this->view =& parent::createDefaultView();
		$this->initializeView($this->view);
		$this->setEvents($this->view);

		return $this->view;
	}

	function onChangeSend($selector, &$target) {
		$this->addEventListener(array('changed'=>$selector), $target);
		$this->setOnChangeEvent($this->view);
	}

	function onFocusSend($selector, &$target) {
		$this->addEventListener(array('focus'=>$selector), $target);
		$this->setOnFocusEvent($this->view);
	}

	function onBlurSend($selector, &$target) {
		$this->addEventListener(array('blur'=>$selector), $target);
		$this->setOnBlurEvent($this->view);
	}
}

?>