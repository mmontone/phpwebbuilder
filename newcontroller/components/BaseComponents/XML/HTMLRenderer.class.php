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
	function &childrenWithId($id){
		$res = array();
		$ks = array_keys ($this->childNodes);
		foreach ($ks as $k){
			$t =& $this->childNodes[$k];
			if ($t->hasId($id)){
				$res[]=&$t;
			}
		}
		return $res;
	}
	function &templatesForClass(&$component){
		$res = array();
		$ks = array_keys ($this->childNodes);
		foreach ($ks as $k){
			$t =& $this->childNodes[$k];
			if ($t->isTemplateForClass($component)){
				$res[]=&$t;
			}
		}
		return $res;
	}
	function &containersForClass(&$component){
		$res = array();
		$ks = array_keys ($this->childNodes);
		foreach ($ks as $k){
			$t =& $this->childNodes[$k];
			if ($t->isContainerForClass($component)){
				$res[]=&$t;
			}
		}
		return $res;
	}
	function isTemplateForClass(&$component){
		return false;
	}
	function isContainerForClass(&$component){
		return false;
	}
	function isContainer(){
		return false;
	}
	function hasId($id){
		$b = ($this->attributes["id"]!==null && strcasecmp($this->attributes["id"],$id)==0);
		return $b; 
	}
}

?>