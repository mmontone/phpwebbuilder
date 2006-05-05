<?php

class DefaultCMS extends Component{
	var $menu;
	var $lastParams = array();
	function initialize(){
		$this->add_component(new Menu, "menu");
		$this->menu->addEventListener(array('on menuClicked'=>'changeBody'),$this);
		$this->add_component(new Login, "body");
		$this->body->addEventListener(array('on menuChanged'=>'updateMenu'),$this);
	}
 	function declare_actions(){}
 	function changeBody(&$menu,&$comp){
		$this->add_component($comp, "body");
 		$this->body->addEventListener(array('on menuChanged'=>'updateMenu'),$this);
 	}
 	function updateMenu(){
 		$this->menu->newmenu();
 	}
}

?>