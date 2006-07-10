<?php

class DefaultCMSApplication extends Application{
	var $templateName = 'Default';
 	function &setRootComponent() {
 		return new DefaultCMS;
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
 	function renderExtraHeaderContent() {
 		return '<!--[if lt IE 7]><script defer="defer" type="text/javascript" src="'.pwb_url.'/Templates/Default/pngfix.js"></script><![endif]-->';
 	}
}

?>