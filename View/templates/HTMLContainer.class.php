<?php

class HTMLContainer extends XMLNodeModificationsTracker {
	function HTMLContainer($tag='', $attrs=array()){
		if (isset($attrs['id']))$id = $attrs['id']; else {$id='';}
		parent::XMLNodeModificationsTracker($tag, $attrs);
		$this->attributes['simpleId'] = $id;
	}
	function renderEcho() {
			$tag_name=Application::defaultTag();
			$fid = $this->getId();
			if (defined('debugview') and constant('debugview')=='1') {
				echo "<$tag_name class=\"hiddencontainer\" id=\"$fid\">".$this->attributes['class'].':'.$this->attributes['simpleId']."</$tag_name>";
			} else {
				echo "<$tag_name style=\"visibility:hidden\" id=\"$fid\"></$tag_name>";
			}

	}

	function renderNonEcho() {
			$tag_name=Application::defaultTag();
			$fid = $this->getId();
			if (defined('debugview') and constant('debugview')=='1') {
				return "<$tag_name class=\"hiddencontainer\" id=\"$fid\">".$this->attributes['class'].':'.$this->attributes['simpleId']."</$tag_name>";
			} else {
				return "<$tag_name style=\"visibility:hidden\" id=\"$fid\"></$tag_name>";
			}

	}

	function getRealId(){
		if ($this->parentNode)$id =$this->parentNode->getRealId();

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