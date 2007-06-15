<?php

class DefaultCMS extends ModuleComponent{
	function getTitle(){
		return sitename.'\'s CMS';
	}
	function &getRootComponent(){
		$comp =& new InitialDefaultCMSComponent;
		return $comp;
	}
}
?>