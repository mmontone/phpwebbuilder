<?php
class Widget extends Component {
	var $value_model;
	var $enqueued_hooks = array ();
	var $disabled;
	var $invalid;
	var $clickable;
	var $events;

	function Widget(& $value_model, $callback_actions = array ()) {
		parent::Component();

		if ($value_model == null) {
			$this->value_model = & new ValueHolder($null = null);
		}
		else {
			#@typecheck $value_model:ValueModel@#
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
		$this->clickable =& new ValueHolder($b3=false);
		$this->events =& new Collection();
		$this->setEvents();
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
	function setEvents() {
		/* Default events, override in subclasses */
		$class = getClass($this);
		$this->events->atPut('onchange', $a=array('onchange',"enqueueChange(getEventTarget(event),{$class}GetValue)"));
	}

	function setOnChangeEvent() {
		$class = getClass($this);
		$this->events->atPut('onchange', $a=array('onchange',"enqueueChange(getEventTarget(event),{$class}GetValue); " . $this->componentChangeJSFunction() . "(getEventTarget(event))"));
	}

	function componentChangeJSFunction() {
		return 'componentChange';
	}

	function setOnBlurEvent() {
		$this->events->atPut('onblur', $a=array('onblur', "return componentBlur(getEventTarget(event));"));
	}

	function setOnFocusEvent() {
		$this->events->atPut('onfocus', $a=array('onfocus', "return componentFocus(getEventTarget(event));"));
	}
	function setEvent($event,  $function){
		$this->events->atPut($event, $a=array($event, $function));
	}
	function setOnClickEvent() {
		$this->clickable->setValue($v=true);
		$this->events->atPut('onclick', $a=array('onclick', 'return '.$this->componentClickedJSFunction() . '(getEventTarget(event));'));
	}

	function componentClickedJSFunction() {
		return 'componentClicked';
	}

	function viewUpdated($params) {
		$new_value =  $this->valueFromForm($params);
		$value = $this->getValue();

		if ($new_value != $value) {
			$this->viewHandler->pauseRegistering();
			$this->setValue($new_value);
		}
	}

	function valueFromForm(& $params) {
		return $params;
	}

	function setValue($value) {
		$this->value_model->setValue($value);
	}

	function getValue() {
		$v = $this->value_model->getValue();
		return $v;
	}
	function onFocusSend($selector, & $target) {
		#@typecheck $selector:string, $target:object@#
		$this->addInterestIn('focus',new FunctionObject($target, $selector));
	}

	function onBlurSend($selector, & $target) {
		#@typecheck $selector:string, $target:object@#
		$this->addInterestIn('blur',new FunctionObject($target, $selector));
	}

	function onClickSend($selector, & $target) {
		#@typecheck $selector:string, $target:object@#
		$this->addInterestIn('click',new FunctionObject($target, $selector));
	}

	function onEnterClickOn(&$comp) {
		#@typecheck $comp:Component@#
		$class = getClass($this);
		$onkeypress = "if(event.which==13||event.keyCode==13) {".
				"enqueueChange(getEventTarget(event),{$class}GetValue);".
				"componentClicked(document.getElementById('" . $comp->getId() . "'));}";

		$this->events->atPut('onkeypress', $a = array('onkeypress',$onkeypress));
	}

	// TODO: fix onEnterFocus
	function onEnterFocus(&$comp) {
		#@typecheck $comp:Component@#
		$this->events->atPut('onkeypress', $a = array('onkeypress', "if(event.which==13||event.keyCode==13) {".
			    "triggerEventIn('change',document.getElementById('" . $this->getId() ."');".
				"document.getElementById('" . $comp->getId() ."').focus();}"));
	}

	function addInterestIn($event, & $event_callback) {
		parent :: addInterestIn($event, $event_callback);
			switch ($event) {
				case 'changed' :
					$this->setOnChangeEvent();
					break;
				case 'blur' :
					$this->setOnBlurEvent();
					break;
				case 'focus' :
					$this->setOnFocusEvent();
					break;
				case 'click' :
					$this->setOnClickEvent();
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
	function valueChanged(){}
}
?>