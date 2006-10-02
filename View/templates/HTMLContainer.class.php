<?php

class HTMLContainer extends XMLNodeModificationsTracker {
	function HTMLContainer($tag='', $attrs=array()){
		if (isset($attrs['id']))$id = $attrs['id']; else {$id='';}
		parent::XMLNodeModificationsTracker($tag, $attrs);
		$this->attributes['simpleId'] = $id;
	}
	function renderEcho() {
			$fid = $this->getId();
			if (defined('debugview') and constant('debugview')=='1') {
				echo "<span class=\"hiddencontainer\" id=\"$fid\">".$this->attributes['class'].':'.$this->attributes['simpleId']."</span>";
			} else {
				echo "<span style=\"visibility:hidden\" id=\"$fid\"></span>";
			}

	}

	function renderNonEcho() {
			$fid = $this->getId();
			if (defined('debugview') and constant('debugview')=='1') {
				return "<span class=\"hiddencontainer\" id=\"$fid\">".$this->attributes['class'].':'.$this->attributes['simpleId']."</span>";
			} else {
				return "<span style=\"visibility:hidden\" id=\"$fid\"></span>";
			}

	}

	function getRealId(){
		$this->parentNode->getRealId();
		$id = $this->parentNode->getAttribute('id');
		$id.= CHILD_SEPARATOR.$this->getAttribute('class');
		$id.= CHILD_SEPARATOR.$this->getAttribute('simpleId');
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
		$c = new HTMLContainer();
		return $c;
	}
}

class XMLVariable extends XMLNodeModificationsTracker {
	function getRealId(){
		return $this->attributes['id'];
	}

}

?>