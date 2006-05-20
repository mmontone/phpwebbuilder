<?php

class DefaultCMS extends Component{
	var $menu;
	var $lastParams = array();
	function initialize(){
		$this->add_component(new Menu, "menu");
		$this->menu->addEventListener(array('menuClicked'=>'changeBody'),$this);
		$this->changeBody($this->menu, new Login);
	}
 	function declare_actions(){}
 	function changeBody(&$menu,&$comp){
		$this->add_component($comp, "body");
 		$this->body->addEventListener(array('menuChanged'=>'updateMenu'),$this);
 	}
 	function updateMenu(){
 		$this->menu->newmenu();
 	}
}

?>