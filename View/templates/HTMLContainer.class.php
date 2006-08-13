<?php

class HTMLContainer extends XMLNodeModificationsTracker {
	function renderEcho() {
		$fid = $this->getId();
		echo "<span style=\"visibility:hidden\" id=\"$fid\"></span>";
	}
	function getRealId(){
		$this->parentNode->getRealId();
		$id = $this->parentNode->getAttribute('id');
		$id.= '/'.$this->getAttribute('class');
		$this->attributes['fakeid'] =$id;
	}
	function getId(){
		$this->getRealId();
		return $this->getAttribute('fakeid');
	}
	function isContainer(){
		return true;
	}
	function isContainerForClass(&$component){
		return is_a($component, $this->getAttribute("class"));
	}
	function &createCopy(){
		$c = new HTMLContainer;
		return $c;
	}
}

?>