<?php

require_once dirname(__FILE__) . '/XMLNode.class.php';

class HTMLRendererNew extends XMLNode{
	function renderPage(){
		$this->tagName='form';
		$this->setAttribute('action',"new_dispatch.php");
		$this->setAttribute('method',"POST");
		$this->setAttribute('enctype',"multipart/form-data");
		$ret="<html>\n" .
				"   <head><script src=\"".site_url."/admin/ajax/ajax.js\"></script></head><body>";
		$ret .= str_replace("\n", "\n   ", $this->render());
		$ret .="\n</body></html>";
		return $ret; 
	}
	function showXML(){
		$ret = $this->renderPage();
		$ret = str_replace("<", "&lt;", $ret );
		$ret = str_replace(">", "&gt;", $ret);
		$ret = str_replace("\n", "<br/>", $ret );
		$ret = str_replace("   ", "&nbsp;&nbsp;&nbsp;", $ret );
		return $ret;
	}
	function &instantiateFor(&$component){
		$component->setView($this);
		return $this; 
	}
	function &create_text_node($text,&$obj){
		return new HtmlTextNode($text,&$obj);
	}
	function childrenWithId($id){
		return array_filter($this->childNodes, 
			create_function('$c', 
				'return $c->id=='.$id.';'));	
	}
	function templatesForClass($class){
		return array_filter($this->childNodes, 
			create_function('$c', 
				'return $c->isTemplateForClass("'.$class.'");'));
	}
	function isTemplateForClass($class){
		return false;
	}
	function isContainer(){
		return false;
	}
}

class HtmlTextNode extends HTMLRendererNew{
	var $text;
	function HtmlTextNode($text,&$obj){
		$this->text = $text;
		$this->controller =&$obj;
	}
	function render (){
		return $this->text; 
	} 
}

?>