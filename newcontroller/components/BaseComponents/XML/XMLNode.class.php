<?php

require_once dirname(__FILE__) . '/DOMXMLNode.class.php';

class XMLNode extends DOMXMLNode
{
	var $controller;
	var $templates = array();
	var $containers = array();

	function XMLNode($tag_name='div', $attributes=array()) {
		return parent::DOMXMLNode($tag_name, $attributes);
	}

	function &create_text_node($text,&$obj){
		return new HtmlTextNode($text,&$obj);
	}

	function &create_element($tag_name, &$controller) {
		$element =& parent::create_element($tag_name);
		$element->controller = & $controller;
		return $element;
	}


	/* Always access attributes through getAttribute */
/*	function getAttribute($attr) {
		$val = parent::getAttribute($attr);
		if (!$val && ($attr == 'id' || $attr = 'name')) {
			if ($this->controller != null) {
				$id = $this->controller->getId();
				$this->setAttribute('id', $id);
				$this->setAttribute('name', $id);
				return $id;
			}
		}
		return $val;
	}*/

	function setAttribute($name, $val){
		$this->attributes[$name] = $val;
	}
	function setId($id) {
		$this->setAttribute('id', $id);
	}
	function getRealId(){
		if ($this->controller!=null) {
			$id = $this->controller->getId();
			$this->setAttribute('id', $id);
			$this->setAttribute('name', $id);
		}
	}
	function render(){
		$this->getRealId();
		$attrs ="";
		foreach ($this->attributes as $name=>$val){
			$attrs .= ' '.$name.'="'.$val.'"';
		}
		if (count($this->childNodes)==0){
			return "<$this->tagName $attrs></$this->tagName>";
		} else {
			$childs = '';
			$ks = array_keys($this->childNodes);
			foreach ($ks as $k) {
				$childs .= $this->childNodes[$k]->render();
			}
			//$childs = str_replace("\n", "\n   ", $childs);
			$ret .="<$this->tagName $attrs>$childs</$this->tagName>";
			return $ret;
		}
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
	function &templatesForClass(&$component){
		$res = array();
		$ks = array_keys ($this->childNodes);
		foreach ($ks as $k){
			$t =& $this->childNodes[$k];
			if ($t->isTemplateForClass($component)){
				$res[]=&$t;
			} else if (!$t->isTemplate()){
				$res2 =& $t->templatesForClass($component);
				$ks2 = array_keys ($res2);
				foreach($ks2 as $k2){
					$res []=& $res2[$k2];
				}
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
			} else if (!$t->isTemplate()){
				$res2 =& $t->containersForClass($component);
				$ks2 = array_keys ($res2);
				foreach($ks2 as $k2){
					$res []=& $res2[$k2];
				}
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
	function isTemplate(){
		return false;
	}
	function hasId($id){
		$b = ($this->attributes["id"]!==null && strcasecmp($this->attributes["id"],$id)==0);
		return $b;
	}

	function getId() {
		return $this->getAttribute('id');
	}

	function checkTree() {
		foreach(array_keys($this->childNodes) as $i) {
			assert($this->childNodes[$i]->parentNode);
			$this->childNodes[$i]->checkTree();
		}
	}
}
?>
