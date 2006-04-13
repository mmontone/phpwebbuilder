<?php

class DefaultCMS extends Component{
	var $menu;
	var $lastParams = array();
	function initialize(){
		$this->add_component(new Menu, "menu");
		$this->menu =& $this->component_at("menu");
		$this->menu->addEventListener(array('on menuClicked'=>'changeBody'),$this);
		$this->changeBody($this->menu,array('Controller'=>'Login'));
	} 
	function prepareToRender(){
		parent::prepareToRender();
		$this->menu->view->setAttribute('style', "float:left");
		$body =& $this->component_at("body"); 
		$body->view->setAttribute('style', "float:left");
	}
 	function declare_actions(){}
 	function changeBody($menu,$params){
  		$this->add_component(new $params["Controller"], "body");
 		$body =& $this->component_at("body");
 		$body->addEventListener(array('on menuChanged'=>'updateMenu'),$this);
 		$body->setForm($params);
 	}
 	function updateMenu(){
 		$this->menu->newmenu();
 	}
}

?>