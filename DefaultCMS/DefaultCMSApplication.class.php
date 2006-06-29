<?php

class DefaultCMSApplication extends Application{
	//var $templateName = 'BFSalud';
	var $templateName = 'Default';
 	function &setRootComponent() {
 		$cms =& new DefaultCMS;
 		return $cms;
 	}
 	function loadTemplates (){
 		$this->viewCreator->loadTemplatesDir(pwbdir . "/DefaultCMS/Templates/".$this->templateName);
 		$this->viewCreator->loadTemplatesDir(basedir . "/MyTemplates/");
 	}
 	function addStyleSheets(){
 		$this->addStyleSheet(pwb_url."/DefaultCMS/Templates/".$this->templateName.'/'.$this->templateName.'.css');
 	}
 	function addAjaxRenderingSpecificScripts(){
 		parent::addAjaxRenderingSpecificScripts();
 		$this->addScript(pwb_url.'/DefaultCMS/Templates/Default/loading.js');
 	}
 	function getTitle(){return sitename.'\'s Content Management System';}
}

?>