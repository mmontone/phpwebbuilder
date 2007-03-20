<?php

class HTMLContainer extends XMLNodeModificationsTracker {
	function HTMLContainer($tag='', $attrs=array()){
		if (isset($attrs['id']))$id = $attrs['id']; else {$id='';}
		parent::XMLNodeModificationsTracker($tag, $attrs);
		$this->attributes['simpleId'] = $id;
	}
	function renderEcho() {
			echo $this->renderNonEcho();
	}
	function renderNonEcho() {
			if (isset($this->attributes['tagname'])) {
				$tag_name=$this->attributes['tagname'];
			} else {
				$tag_name=Application::defaultTag();
			}
			$fid = $this->getId();
			if (defined('debugview') and constant('debugview')=='1') {
				return "<$tag_name class=\"hiddencontainer\" id=\"$fid\">".$this->attributes['class'].':'.$this->attributes['simpleId']."</$tag_name>";
			} else {
				return "<script id=\"$fid\">0;</script>";
				//return "<$tag_name style=\"visibility:hidden\" id=\"$fid\">&amp;nbsp;</$tag_name>";
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
		return $component->hasType($this->getClass());
	}
	function &createCopy(){
		$c = new HTMLContainer();
		return $c;
	}
	function getClass() {
		return strtolower($this->getAttribute('class'));
	}
}

class XMLVariable extends XMLNodeModificationsTracker {
	function getRealId(){
		return $this->attributes['id'];
	}

}

?>