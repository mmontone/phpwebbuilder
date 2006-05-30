<?php

require_once dirname(__FILE__) . '/../../Component.class.php';

class FormComponent extends Component
{
	var $value_model;
	var $enqueued_hooks = array();

	function FormComponent(&$value_model, $callback_actions=array()) {
		if ($value_model==null) {
			$this->value_model =& new ValueHolder($null = null);
		} else {
			$this->value_model =& $value_model;
		}
		$this->value_model->onChangeSend('valueChanged', $this);
		$this->enqueued_hooks = array();
		parent::Component($callback_actions);
	}

	function setView(&$view) {
		parent::setView($view);
		$this->setEnqueuedHooks($view);
	}

	function setEnqueuedHooks(&$view) {
		foreach(array_keys($this->enqueued_hooks) as $i) {
			$this->enqueued_hooks[$i]->callWith($view);
		}
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

	function setOnClickEvent(&$view) {
		$view->setAttribute('onclick', "javascript:componentClicked(this)");
	}

	function valueChanged(){}

	function viewUpdated($params) {
		$new_value =& $this->valueFromForm($params);
		$value =& $this->getValue();

		if ($new_value != $value)
			$this->setValue($new_value);
	}

	function valueFromForm(&$params) {
		return $params;
	}

	function setValue(&$value) {
		$this->value_model->setValue($value);
	}

	function &getValue() {
		return $this->value_model->getValue();
	}
	function &createDefaultView(){
		$this->view =& parent::createDefaultView();
		$this->initializeView($this->view);
		$this->setEvents($this->view);

		return $this->view;
	}

	function setHook(&$hook) {
		if ($this->view)
			$hook->callWith($this->view);
		else
			$this->enqueueHook($hook);
	}

	function enqueueHook(&$hook) {
		$this->enqueued_hooks[] = $hook;
	}

	function onChangeSend($selector, &$target) {
		$this->addEventListener(array('changed'=>$selector), $target);
	}

	function onFocusSend($selector, &$target) {
		$this->addEventListener(array('focus'=>$selector), $target);
	}

	function onBlurSend($selector, &$target) {
		$this->addEventListener(array('blur'=>$selector), $target);
	}

	function onClickSend($selector, &$target) {
		$this->addEventListener(array('click'=>$selector), $target);
	}

	function addEventListener($event_specs, &$listener) {
        parent::addEventListener($event_specs, $listener);

        foreach ($event_specs as $event_selector => $event_callback) {
            switch ($event_selector) {
            	case 'change' : $this->setHook(new FunctionObject($this, 'setOnChangeEvent'));
            					break;
            	case 'blur' : $this->setHook(new FunctionObject($this, 'setOnBlurEvent'));
            					break;
            	case 'focus' : $this->setHook(new FunctionObject($this, 'setOnFocusEvent'));
            					break;
            	case 'click' : $this->setHook(new FunctionObject($this, 'setOnClickEvent'));
            					break;
            }
        }

    }


	function &printValue() {
		return $this->getValue();
	}
}

?>