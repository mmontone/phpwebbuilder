<?php

class DefaultCMSApplication extends Application{
 	function &set_root_component() {
 		return new DefaultCMS;
 	}
 	function loadTemplates (){
 		$this->viewCreator->parseTemplates(array(pwbdir."/Controllers/DefaultCMS/Templates/template.xml"));
/* 		$temp=& new HTMLTemplate;
 		$temp->setAttribute("class", "Menu");
 		$menu=& new HTMLRendererNew;
 		$menu->setAttribute("style", "min-width:25%;float:left");
 		$temp->append_child($menu);
 		
 		$link=& new HTMLContainer;
 		$link->setAttribute('class','Component');
 		$menu->append_child($link);
 		
 		$this->viewCreator->setTemplates(array($temp));*/
 	}
}

?>