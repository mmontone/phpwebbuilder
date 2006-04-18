<?php


class HTMLTextNode extends HTMLRendererNew{
	var $text;
	function HTMLTextNode($text,&$obj){
		$this->text = $text;
		$this->controller =&$obj;
	}
	function render (){
		return $this->text;
	} 
}

?>