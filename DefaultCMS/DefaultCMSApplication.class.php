<?php

class DefaultCMSApplication extends Application{
 	function &set_root_component() {
 		$cms =& new DefaultCMS;
 		return $cms;
 	}
 	function loadTemplates (){
 		$this->viewCreator->loadTemplatesDir(pwbdir."/DefaultCMS/Templates");
 	}
 	function &setCss(){
 		return array(site_url."/admin/DefaultCMS/standard.css");
 	}
}

?>