<?php

class XMLNode {
	var $childNodes = array();
	var $parent = null;
	var $tagName = "div";
	var $attributes = array();
	var $controller;
	function &create_element($tag, &$obj){
		$cn = get_class($this);
		$e =& new $cn;
		$e->tagName = $tag;
		$e->controller =&$obj;
		return $e;
	}
	function &parent(){
		if ($this->parent!=null){
			return $this->parent;
		} else {
			print_backtrace("there is no parent");
		}
	}
	function setTagName($tagName){
		$this->tagName = $tagName;
	}
	function &create_text_node($text,&$obj){
		return new XMLTextNode($text,&$obj);
	}

	function append_child(&$xml){
		$this->childNodes[]=&$xml;
		$xml->parent =& $this;
	}
	function replace_child(&$old, &$new){
		$ks = array_keys($this->childNodes);
		$countreps =0;
		foreach($ks as $k){
			if ($this->childNodes[$k]->getRealId()==$old->getRealId()){
				$countreps++;
				$this->childNodes[$k]=&$new;
				$new->parent =& $this;
				unset($old->parent);
			}
		}
		if ($countreps ==0) echo "replace unsuccessful";
		//echo $new->getRealId();
	}
	function setAttribute($name, $val){
		$this->attributes[$name] = $val;
	}
	function setId($id) {
		return $this->id = $id; 
	} 
	function getRealId(){
		return $this->controller->getId(); 
	}
	function render(){
		$id = $this->getRealId();
		$attrs='id="'.$id.'"';
		$attrs.=' name="'.$id.'"';
		foreach ($this->attributes as $name=>$val){
			$attrs .= ' '.$name.'="'.$val.'"';
		}
		
		if (count($this->childNodes)==0){
			return "\n<$this->tagName $attrs />";
		} else {
			$childs = "";
			$ks = array_keys($this->childNodes);
			foreach($ks as $k){
				$childs .=$this->childNodes[$k]->render();
			}
			$childs = str_replace("\n", "\n   ", $childs);
			$ret .="\n<$this->tagName $attrs>".$childs."\n</$this->tagName>";
			return $ret; 
		}
	}
}

class XMLTextNode extends XMLNode{
	var $text;
	function XMLTextNode($text,&$obj){
		$this->text = $text;
		$this->controller =&$obj;
	}
	function render (){
		return $this->text; 
	} 
	
	
}

?>