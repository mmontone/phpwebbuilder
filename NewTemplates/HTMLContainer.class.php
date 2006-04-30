<?php

//require_once dirname(__FILE__) . '/HTMLRendererNew.class.php';

class HTMLContainer extends XMLNodeModificationsTracker {
	function HTMLContainer() {
		parent::XMLNodeModificationsTracker();
	}
	function render () {
		return "";
	}
	function isContainer(){
		return true;
	}
	function isContainerForClass(&$component){
		return is_a($component, $this->attributes["class"]);
	}
	function createCopy(){
		return new HTMLContainer;
	}
}

?>