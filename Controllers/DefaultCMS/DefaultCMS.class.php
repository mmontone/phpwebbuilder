<?php

class DefaultCMS extends Component{
	var $menu;
	var $body;
	var $lastParams = array();
	function initialize(){
		$this->add_component(new Menu, 0);
		$this->menu =& $this->component_at(0);
		$this->menu->addEventListener(array('on menuChanged'=>'changeBody'),$this);
		$this->add_component(new Login, 1);
		$this->body =& $this->component_at(1);
	} 
 	function declare_actions(){}
 	function render_on(&$html){
 		$this->menu->renderContent($html);
 		$html->text("<div class=\"body\" style=\"float:left;max-width:75%\">");
 		$_REQUEST=$this->lastParams;
 		$this->body->renderContent($html);
 		$html->text("</div>");
 	}
 	function changeBody($menu,$params){
 		$this->add_component(new $params["Controller"], 1);
 		$this->body =& $this->component_at(1);
 		$this->lastParams = $params;
 	}
}

?>