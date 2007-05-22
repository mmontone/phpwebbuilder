<?php

class WidgetHTMLHandler extends ComponentHTMLHandler{
	function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$this->initializeDefaultView($v);
		return $v;
	}
	function setEvents(&$comp){
		parent::setEvents($comp);
		$comp->events->onChangeSend('updateEvent',$this);
		$comp->disabled->onChangeSend('updateDisabled',$this);
		$comp->value_model->onChangeSend('doValueChanged',$this);
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

	function updateDisabled(&$vh){
		if ($vh->getValue()) {
			$this->view->setAttribute('disabled','disabled');
		} else {
			$this->view->removeAttribute('disabled');
		}
	}
	function setView(& $view) {
		parent :: setView($view);
		$this->initializeView($view);
		$this->updateDisabled($this->component->disabled);
		$this->updateEvents($this->component->events);
	}
	function initializeView(){}
	function initializeDefaultView(){}
}
?>