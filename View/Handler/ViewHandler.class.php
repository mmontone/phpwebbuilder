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
	}
	function &defaultView(){
		$v =& $this->createDefaultView();
		$this->setView($v);
		return $v;
	}
	function setView(&$view){
		$this->component->view =& $view;
		$view->controller =& $this->component;
		$this->component->setView($view);
		$this->view =& $view;
	}
	function &instantiateTemplate(&$template){
		$v =& $template->instantiate();
		$this->setView($v);
		return $v;
	}
}
?>