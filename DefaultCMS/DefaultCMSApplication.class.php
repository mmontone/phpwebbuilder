<?php

class DefaultCMSApplication extends Application{
 	function &set_root_component() {
 		$cms =& new DefaultCMS; 		
 		return $cms; 
 	}
 	function loadTemplates (){
 		$this->viewCreator->parseTemplates(array(pwbdir."/DefaultCMS/Templates/template.xml"));
 	}
}

?>