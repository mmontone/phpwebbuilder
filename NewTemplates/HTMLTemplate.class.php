<?php

class HTMLTemplate extends HTMLRendererNew{
	function &instantiateFor(&$component){
		if (count($this->childNodes)!=1){
			$tv =& new HTMLRendererNew;
			foreach($this->childNodes as $c){
				$tv->append_child($c);
			}
		} else {
			$tv = $this->first_child();
		}
		$component->setView($tv);
		return $tv; 
	}
	function &xml2template(&$xml){
		if (strcasecmp($xml->tagName, "template")==0){
			$temp =& new HTMLTemplate; 
		} else if (strcasecmp($xml->tagName, "container")==0){
			$temp =& new HTMLContainer;
		} else if (strcasecmp(get_class($xml), "XMLTextNode")==0){
			$n = null;
			$temp =& new HTMLTextNode($xml->text, $n);
		} else {
			$temp =& new HTMLRendererNew;
		}
		foreach ($xml->childNodes as $c){
			$temp->append_child($this->xml2template($c));
		}
		$temp->id = $xml->id;
		$temp->attributes = $xml->attributes;
		$temp->tagName = $xml->tagName;
		return $temp; 
	} 
	function isTemplateForClass(&$component){
		$b = is_a($component, $this->attributes["class"]);
		return $b;
	}
	function isContainerForClass(&$component){
		return is_a($component, $this->attributes["class"]);
	}	
	function createCopy(){
		return new HTMLContainer; 
	}	
}

class HTMLContainer extends HTMLRendererNew{
	function render (){
		return "<container id=\"".$this->id."\"/>"; 
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