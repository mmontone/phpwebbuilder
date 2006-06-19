<?php

class InstallApplication extends Application{
	function &setRootComponent(){
		return new InstallComponent();
	}
 	function loadTemplates (){
 		$this->viewCreator->loadTemplatesDir(dirname(__FILE__).'/templates/');
 	}
}
?>