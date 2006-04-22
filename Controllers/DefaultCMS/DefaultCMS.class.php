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
 	function changeBody(&$menu,$params){
 		$this->body->stopAndCall (new $params["Controller"]);
 		$this->body->addEventListener(array('on menuChanged'=>'updateMenu'),$this);
 		$this->body->setForm($params);
 	}
 	function updateMenu(){
 		$this->menu->newmenu();
 	}
}

?>