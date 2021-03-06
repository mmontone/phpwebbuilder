<?php

class ViewHandler extends PWBFactory{
	var $component;
	var $view;
	var $registering = true;
	function &createInstanceFor(&$component){
		$this->setComponent($component);
		return $this;
	}
	function release(){
		parent::release();
		$this->view->release();
	}

	function setComponent(&$component){
		$this->component =& $component;
		$component->viewHandler =& $this;
	}

	function &getComponent() {
		return $this->component;
	}

	function &defaultView(){
		$v =& $this->createDefaultView();
		$this->setView($v);
		return $v;
	}
	function redraw(){
		if ($this->view){
			$this->view->redraw();
		}
	}
	function pauseRegistering(){
		if ($this->view){
			$this->registering = false;
		}
	}
	function setView(&$view){
		$this->component->view =& $view;
		$view->controller =& $this->component;
		$this->view =& $view;
		$view->attributes['id'] = $this->component->getId();
	}

	function &getView() {
		$this->view;
	}
	function &instantiateTemplate(&$template){
		$v =& $template->instantiate();
		$this->setView($v);
		return $v;
	}
	function prepareToRender() {}
	function printString() {
    	return $this->primPrintString($this->component->printString());
    }
}
?>