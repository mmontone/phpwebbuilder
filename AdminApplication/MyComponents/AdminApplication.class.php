<?php

class AdminApplication extends ContextualComponent{
	function AdminApplication(&$PWBAppConfig){
		$this->application=&$PWBAppConfig;
		parent::ContextualComponent();
	}
	function initialize() {
		parent::initialize();
		$this->addComponent(new Label($this->application->printString()));
		$this->addNavigationMenu('DB Administration',new FunctionObject($this, 'goToDBAdministration'));
		$this->addNavigationMenu('Edit Configuration',new FunctionObject($this, 'goToEditConfiguration'));
	}
	function goToDBAdministration(){

	}
	function goToEditConfiguration(){

	}
}

?>