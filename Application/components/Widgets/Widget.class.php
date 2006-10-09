<?php
class Widget extends Component {
	var $value_model;
	var $enqueued_hooks = array ();
	var $disabled;
	var $invalid;
	function Widget(& $value_model, $callback_actions = array ()) {
		if ($value_model == null) {
			$this->value_model = & new ValueHolder($null = null);
		}
		else {
			$this->value_model = & $value_model;
		}
		$this->value_model->addEventListener(array (
			'changed' => 'valueChanged',
			'validated' => 'fieldValidated',
			'invalid' => 'fieldInvalid',
			'required_but_empty' => 'fieldRequiredButEmpty'
		), $this);
		$this->invalid =& new ValueHolder($b1=false);
		$this->disabled =& new ValueHolder($b2=false);
		parent::Component();
		$this->registerCallbacks($callback_actions);
	}

	function fieldValidated(&$field) {
		$this->invalid->setValue($b=false);
	}

	function fieldInvalid(&$field) {
		$this->invalid->setValue($b=true);
	}

	function fieldRequiredButEmpty(&$field) {
		$this->invalid->setValue($b=true);
	}
	function setEnqueuedHooks(& $view) {
		foreach (array_keys($this->enqueued_hooks) as $i) {
			$this->enqueued_hooks[$i]->callWith(&$view);
		}
	}

	function setEvents(& $view) {
		/* Default events, override in subclasses */
		$class = getClass($this);
		$view->setAttribute('onchange', "javascript:enqueueChange(getEventTarget(event),{$class}GetValue)");
	}

	function setOnChangeEvent(& $view) {
		$class = getClass($this);
		$view->setAttribute('onchange', "javascript:enqueueChange(getEventTarget(event),{$class}GetValue); componentChange(getEventTarget(event))");
	}

	function setOnBlurEvent(& $view) {
		$view->setAttribute('onblur', "javascript:componentBlur(getEventTarget(event))");
	}

	function setOnFocusEvent(& $view) {
		$view->setAttribute('onfocus', "javascript:componentFocus(getEventTarget(event))");
	}

	function setOnClickEvent(& $view) {
		$view->setAttribute('onclick', "componentClicked(getEventTarget(event));");
		$view->addCSSClass('clickable');
	}
	function viewUpdated($params) {
		$new_value = & $this->valueFromForm($params);
		$value = & $this->getValue();

		if ($new_value != $value) {
			$this->setValue($new_value);
		}
	}

	function valueFromForm(& $params) {
		return $params;
	}

	function setValue(& $value) {
		$this->value_model->setValue($value);
	}

	function & getValue() {
		return $this->value_model->getValue();
	}
	function setHook(& $hook) {
		if ($this->view)
			$hook->callWith($this->view);
		else
			$this->enqueueHook($hook);
	}

	function enqueueHook(& $hook) {
		$this->enqueued_hooks[] = $hook;
	}

	function onChangeSend($selector, & $target) {
		$this->addEventListener(array (
			'changed' => $selector
		), $target);
	}

	function onFocusSend($selector, & $target) {
		$this->addEventListener(array (
			'focus' => $selector
		), $target);
	}

	function onBlurSend($selector, & $target) {
		$this->addEventListener(array (
			'blur' => $selector
		), $target);
	}

	function onClickSend($selector, & $target) {
		$this->addEventListener(array (
			'click' => $selector
		), $target);
	}

	function addInterestIn($event, & $event_callback) {
		parent :: addInterestIn($event, & $event_callback);
			switch ($event) {
				case 'changed' :
					$this->setHook(new FunctionObject($this, 'setOnChangeEvent'));
					break;
				case 'blur' :
					$this->setHook(new FunctionObject($this, 'setOnBlurEvent'));
					break;
				case 'focus' :
					$this->setHook(new FunctionObject($this, 'setOnFocusEvent'));
					break;
				case 'click' :
					$this->setHook(new FunctionObject($this, 'setOnClickEvent'));
					break;
			}
	}

	function & printValue() {
		return $this->getValue();
	}

	function disable() {
		$this->enable(false);
	}

	function enable($value=true) {
		$this->disabled->setValue($value=!$value);
	}

	function getWidgets(&$ws){
		$ws[$this->getId()]=&$this;
		parent::getWidgets($ws);
	}
}
?>