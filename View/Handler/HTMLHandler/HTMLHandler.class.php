<?php

class HTMLHandler extends ViewHandler{}

class ComponentHTMLHandler extends HTMLHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker;
		$t =& new HTMLContainer('',array('class'=>'Component'));
		$v->appendChild($t);
		return $v;
	}
	function setComponent(&$comp){
		parent::setComponent($comp);
		$this->setEvents($comp);
	}
	function setEvents(&$comp){
		$comp->componentstates->onChangeSend('updateStates',$this);
	}
	function setView(& $view) {
		parent :: setView($view);
		foreach($view->css_classes as $class){
			$this->component->componentstates->add($class);
		}
		$this->updateStates($this->component->componentstates, $n=null);
	}
	function updateState(&$col, $ev){
		$this->view->addCSSClass($ev);
	}
	function updateStates(&$col, $ev){
		$this->view->removeCSSClasses();
		foreach($col->elements() as $e){
			$this->updateState($col, $e);
		}
	}
}
?>