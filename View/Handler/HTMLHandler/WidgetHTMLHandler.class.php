<?php

class WidgetHTMLHandler extends HTMLHandler{
	function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$this->initializeDefaultView($v);
		return $v;
	}
	function setComponent(&$comp){
		parent::setComponent($comp);
		$comp->invalid->onChangeSend('updateInvalid',$this);
		$comp->events->onChangeSend('updateEvent',$this);
		$comp->disabled->onChangeSend('updateDisabled',$this);
		$comp->value_model->onChangeSend('valueChanged',$this);
		$comp->clickable->onChangeSend('updateClickable',$this);
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
		$self =& $this;
		$col->map($f=lambda('&$e', '$self->updateEvent($col, $e);', get_defined_vars()));
		delete_lambda($f);
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
	}
	function initializeView(){}
	function initializeDefaultView(){}
}
?>