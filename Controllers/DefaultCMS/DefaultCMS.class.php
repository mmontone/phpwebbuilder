<?php

class DefaultCMS extends Component{
	var $menu;
	var $lastParams = array();
	function initialize(){
		$this->add_component(new Menu, 0);
		$this->menu =& $this->component_at(0);
		$this->menu->addEventListener(array('on menuClicked'=>'changeBody'),$this);
		$this->changeBody($this->menu,array('Controller'=>'Login'));
	} 
 	function declare_actions(){}
 	function render_on(&$html){
 		$menu =& $this->component_at(0);
 		$menu->renderContent($html);
 		$html->text("<div class=\"body\" style=\"float:left;max-width:75%\">");
 		$body =& $this->component_at(1);
 		$body->renderContent($html);
 		$html->text("</div>");
 	}
 	function changeBody($menu,$params){
 		$this->add_component(new $params["Controller"], 1);
 		$body =& $this->component_at(1);
 		$body->addEventListener(array('on menuChanged'=>'updateMenu'),$this);
 		$body->setForm($params);
 	}
 	function updateMenu(){
 		$this->menu->newmenu();
 	}
}

?>