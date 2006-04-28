<?php

require_once dirname(__FILE__) . '/XMLNode.class.php';

class HTMLRendererNew extends XMLNode{
	var $templates = array();
	var $containers = array();
	function renderPage(){
		$this->tagName='form';
		$this->setAttribute('action','new_dispatch.php');
		$this->setAttribute('method','POST');
		$this->setAttribute('enctype','multipart/form-data');
		$ret='<html>'."\n" .
				'   <head><script src="'.site_url.'/admin/ajax/ajax.js"></script></head><body>';
		$ret .= str_replace("\n", "\n   ", $this->render());
		$ret .="\n".'</body></html>';
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
	/** decorar */
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
			} else if(!isset($t->attributes["id"]) && strcasecmp(get_class($t), "HTMLTemplate")!=0) {
				$res2 =& $t->childrenWithId($id);
				$ks2 = array_keys ($res2);
				foreach($ks2 as $k2){
					$res []=& $res2[$k2];
				}
			}
		}
		return $res;
	}
	function getTemplatesAndContainers(){
			$temp = array();
			$cont = array();
			$cn =& $this->childNodes;
			$ks = array_keys ($cn);
			foreach ($ks as $k){
				$t =& $cn[$k];
				if ($t->isTemplate()){
					$temp[]=&$t;
					$cont[]=&$t;
				} else if ($t->isContainer()){
					$cont[]=&$t;
				} else {
					$t->getTemplatesAndContainers();
					$this->addTemplatesAndContainersChild($t);
				}
			}
			$this->addTemplatesAndContainers($temp, $cont);
	}
	function addTemplatesAndContainers(&$temp, &$cont){
			$t =& $this->templates;
			$c =& $this->containers;
			$ks2 = array_keys ($temp);
			foreach($ks2 as $k2){
				$t []=& $temp[$k2];
			}
			$ks3 = array_keys ($cont);
			foreach($ks3 as $k3){
				$c []=& $cont[$k3];
			}
	}
	function addTemplatesAndContainersChild(&$v){
		$this->addTemplatesAndContainers($v->templates, $v->containers);
	}
	function &containersForClass(&$component){
		$res = array();
		$cs =& $this->containers;
		$ks = array_keys ($cs);
		foreach ($ks as $k){
			$t =& $cs[$k];
			if ($t->isContainerForClass($component)){
				$res[]=&$t;
			}
		}
		return $res;
	}
	function &templatesForClass(&$component){
		$res = array();
		$cs =& $this->templates;
		$ks = array_keys ($cs);
		foreach ($ks as $k){
			$t =& $cs[$k];
			if ($t->isTemplateForClass($component)){
				$res[]=&$t;
			}
		}
		return $res;
	}
	/** se delegan */
	function isTemplateForClass(&$component){
		return false;
	}
	function isContainerForClass(&$component){
		return false;
	}
	function isContainer(){
		return false;
	}
	function isTemplate(){
		return false;
	}
	function hasId($id){
		$b = ($this->attributes["id"]!==null && strcasecmp($this->attributes["id"],$id)==0);
		return $b;
	}
}

?>