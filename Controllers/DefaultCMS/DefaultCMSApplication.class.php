<?php

class DefaultCMSApplication extends Application{
 	function &set_root_component() {
 		return new DefaultCMS;
 	}
 	function loadTemplates (){
 		$this->viewCreator->parseTemplates(array(pwbdir."/Controllers/DefaultCMS/Templates/template.xml"));
 	}
}

?>