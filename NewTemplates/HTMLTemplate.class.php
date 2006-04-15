<?php

class HTMLTemplate extends HTMLRendererNew{
	function &instantiateFor(&$component){
		$v = $this->first_child();
		$component->setView($v);
		return $v; 
	}
	function render (){
		return ""; 
	}
	function &xml2template(&$html){
		if (strcasecmp($html->tagName, "template")==0){
			$temp =& new HTMLTemplate; 
		} else if (strcasecmp($html->tagName, "container")==0){
			$temp =& new HTMLContainer;
		} else {
			$temp =& new HTMLRendererNew;
		}
		foreach ($html->childNodes as $c){
			$temp->append_child($this->html2template($c));
		}
		$temp->id = $html->id;
		$temp->attributes = $html->attributes;
		$temp->tagName = $html->tagName;
		return $temp; 
	} 
	function isTemplateForClass($class){
		return strcasecmp($this->attributes["class"],$class)==0;
	}
}

class HTMLContainer extends HTMLRendererNew{
	function render (){
		return ""; 
	} 
	function isContainer(){
		return true;
	}
	
}

?>