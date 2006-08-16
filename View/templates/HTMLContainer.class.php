<?php

class HTMLContainer extends XMLNodeModificationsTracker {
	function renderEcho() {
			$fid = $this->getId();
			if (constant('debugview')=='1') {
				echo "<span class=\"hiddencontainer\" id=\"$fid\">Container: $fid</span>";
			} else {
				echo "<span style=\"visibility:hidden\" id=\"$fid\"></span>";
			}

	}
	function getRealId(){
		$this->parentNode->getRealId();
		$id = $this->parentNode->getAttribute('id');
		$id.= CHILD_SEPARATOR.$this->getAttribute('class');
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

class XMLVariable extends XMLNodeModificationsTracker {
	function getRealId(){
		return $this->attribute['id'];
	}

}

?>