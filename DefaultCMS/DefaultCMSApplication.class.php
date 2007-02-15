<?php

#@preprocessor
$cr =& ConfigReader::Instance();
Compiler::usesClass(__FILE__,$cr->getAttribute('app_class'));
Compiler::usesClass(__FILE__,'DBController');
Compiler::usesClass(__FILE__,'RolesController');
Compiler::usesClass(__FILE__,'Logout');
//@#

class DefaultCMSApplication extends Application{
	//var $templateName = 'Default';
	var $templateName = 'Aqua';
 	function &setRootComponent() {
 		return new DefaultCMS;
 	}

 	function loadTemplates (){
 		$this->viewCreator->loadTemplatesDir(pwbdir . "DefaultCMS/Templates/".$this->templateName.'/');
 		$this->viewCreator->loadTemplatesDir(pwbdir . "DefaultCMS/Templates/Default/");
 		$this->viewCreator->loadTemplatesDir(basedir . "MyTemplates/");
 	}
 	function addStyleSheets(){
 		$this->addStyleSheet(constant('pwb_url') . "DefaultCMS/Templates/".$this->templateName.'/'.$this->templateName.'.css');
 		$this->addStyleSheet(constant('pwb_url') .'lib/calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css');
 	}
	function addScripts() {
		$this->addScript(constant('pwb_url') .'lib/calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js');
	}
 	function addAjaxRenderingSpecificScripts(){
 		parent::addAjaxRenderingSpecificScripts();
 		$this->addScript(pwb_url.'DefaultCMS/Templates/Default/loading.js');
 	}
 	function getTitle(){return sitename.'\'s Content Management System';}
 	function renderExtraHeaderContent() {
 		return '<!--[if lt IE 7]><script defer="defer" type="text/javascript" src="'.pwb_url.'/Templates/Default/pngfix.js"></script><![endif]-->';
 	}
}

?>