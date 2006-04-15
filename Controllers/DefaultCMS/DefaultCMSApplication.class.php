<?php

class DefaultCMSApplication extends Application{
 	function &set_root_component() {
 		return new DefaultCMS;
 	}
 	function loadTemplates (){
 		$temp=& new HTMLTemplate;
 		$temp->setAttribute("class", "menu");
 		$menu=& new HTMLRendererNew;
 		$menu->setAttribute("style", "min-width:25%;float:left");
 		$temp->append_child($menu);
 		$this->viewCreator->setTemplates(array($temp));
 	}
}

?>