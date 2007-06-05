<?php

class DefaultCMS extends ModuleComponent{
	function getTitle(){
		return sitename.'\'s CMS';
	}
	function &getRootComponent(){
		return new InitialDefaultCMSComponent;
	}
}
?>