<?php

require_once dirname(__FILE__)."/XMLNodeModificationsTracker.class.php";

class HTMLTextNode extends XMLNodeModificationsTracker
{
	var $text;
	function HTMLTextNode($text,&$obj){
		parent::XMLNodeModificationsTracker();
		$this->text = $text;
		$this->controller =&$obj;
	}
	function render (){
		return $this->text;
	}
}

?>