<?php

class ViewHandler extends PWBFactory{
	var $component;
	var $view;
	function &createInstanceFor(&$component){
		$this->setComponent($component);
		return $this;
	}
	function setComponent(&$component){
		$this->component =& $component;
		$component->viewHandler =& $this;
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
	function setView(&$view){
		$this->component->view =& $view;
		$view->controller =& $this->component;
		$this->view =& $view;
	}
	function &instantiateTemplate(&$template){
		$v =& $template->instantiate();
		$this->setView($v);
		return $v;
	}
	function prepareToRender() {}
}
?>