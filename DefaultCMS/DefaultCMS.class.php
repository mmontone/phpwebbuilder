<?php

class DefaultCMS extends Component{
	var $menu;
	function initialize(){
		$menu =& $this->addComponent(new Menu, 'menu');
		$this->addComponent(new Label(sitename.'\'s CMS'), "SiteName");
		$this->menu->addEventListener(array('menuClicked'=>'changeBody'),$this);
	}
	function start(){
		$this->changeBody($this->menu, new Login);
	}
 	function changeBody(&$menu,&$comp){
		$this->addComponent($comp, 'body');
 		$comp->addEventListener(array('menuChanged'=>'updateMenu'),$this);
 		$comp->addEventListener(array('logged'=>'login'),$this);
 	}
 	function login(){}
 	function updateMenu(){
 		$this->menu->newmenu();
 	}
}

?>