<?php

class DefaultCMS extends Component{
	var $menu;
	var $lastParams = array();
	function initialize(){
		$this->add_component(new Menu, "menu");
		$this->add_component(new Login, "body");
		$this->body->addEventListener(array('on menuChanged'=>'updateMenu'),$this);
		$this->menu->addEventListener(array('on menuClicked'=>'changeBody'),$this);
	}
 	function declare_actions(){}
 	function changeBody(&$menu,&$comp){
 		$this->body->stopAndCall($comp);
 		$this->body->addEventListener(array('on menuChanged'=>'updateMenu'),$this);
 	}
 	function updateMenu(){
 		$this->menu->newmenu();
 	}
}

?>