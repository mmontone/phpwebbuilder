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
 	function addStyleSheets(){
 		$this->addStyleSheet(pwb_url."/DefaultCMS/Templates/".$this->templateName.'/'.$this->templateName.'.css');
 	}
 	function &addScripts(){
 		$this->addScript(pwb_url.'/DefaultCMS/Templates/Default/loading.js');
 	}

}

?>