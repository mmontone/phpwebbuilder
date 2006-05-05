<?php

class DefaultCMSApplication extends Application{
	//var $templateName = 'BFSalud';
	var $templateName = 'Default';
 	function &set_root_component() {
 		$cms =& new DefaultCMS;
 		return $cms;
 	}
 	function loadTemplates (){
 		$this->viewCreator->loadTemplatesDir(pwbdir . "/DefaultCMS/Templates/".$this->templateName);
 	}
 	function &setCss(){
 		return array(pwb_url."/DefaultCMS/Templates/".$this->templateName.'/'.$this->templateName.'.css');
 	}
}

?>