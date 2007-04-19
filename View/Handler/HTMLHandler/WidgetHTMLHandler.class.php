<?php

class WidgetHTMLHandler extends HTMLHandler{
	function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$this->initializeDefaultView($v);
		return $v;
	}
	function setComponent(&$comp){
		parent::setComponent($comp);
		$this->setEvents($comp);
	}
	function setEvents(&$comp){
		$comp->invalid->onChangeSend('updateInvalid',$this);
		$comp->events->onChangeSend('updateEvent',$this);
		$comp->disabled->onChangeSend('updateDisabled',$this);
		$comp->value_model->onChangeSend('doValueChanged',$this);
		$comp->clickable->onChangeSend('updateClickable',$this);
	}
	function doValueChanged(& $value_model, &$params){
		$reg = $this->view->registering;
		if (!$this->registering) {
			$this->view->registering=false;
			$this->registering = true;
		}
		$this->valueChanged($value_model, $params);
		$this->view->registering = $reg;
	}
	function valueChanged(& $value_model, &$params) {
		if ($this->view){
			$this->prepareToRender();
			$this->redraw();
		}
	}

	function updateEvent(&$col, &$ev){
		$this->view->setAttribute($ev[0], $ev[1]);
	}
	function updateEvents(&$col){
		foreach($col->elements() as $e){
			$this->updateEvent($col, $e);
		}
	}
	function updateInvalid(&$vh){
		if ($vh->getValue()){
			$this->view->addCSSClass('invalid');
		} else {
			$this->view->removeCSSClass('invalid');
		}
	}
	function updateDisabled(&$vh){
		if ($vh->getValue()) {
			$this->view->setAttribute('disabled','disabled');
		} else {
			$this->view->removeAttribute('disabled');
		}
	}
	function updateClickable(&$vh){
		if ($vh->getValue()){
			$this->view->addCSSClass('clickable');
		} else {
			$this->view->removeCSSClass('clickable');
		}
	}
	function setView(& $view) {
		parent :: setView($view);

		$this->initializeView($view);
		$this->updateInvalid($this->component->invalid);
		$this->updateDisabled($this->component->disabled);
		$this->updateEvents($this->component->events);
		$this->updateClickable($this->component->clickable);
	}
	function initializeView(){}
	function initializeDefaultView(){}
}
?>