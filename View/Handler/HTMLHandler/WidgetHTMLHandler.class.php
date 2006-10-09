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
		$comp->disabled->onChangeSend('updateDisabled',$this);
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
	function setView(& $view) {
		parent :: setView($view);
		$this->component->setEvents($view);
		$this->component->setEnqueuedHooks($view);
		$this->initializeView($view);
		$this->prepareToRender();
		$this->updateInvalid($this->component->invalid);
		$this->updateDisabled($this->component->disabled);
	}
	function initializeView(){}
	function prepareToRender() {}
}
?>