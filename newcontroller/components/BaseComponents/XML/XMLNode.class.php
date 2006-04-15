<?php

class XMLNode {
	var $childNodes = array();
	var $parent = null;
	var $tagName = "div";
	var $attributes = array();
	var $controller;
	var $parentPosition = null;
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
	function &first_child(){
		return $this->childNodes[0];
	}
	function append_child(&$xml){
		$this->insert_in($xml,count($this->childNodes));
	}
	function insert_in(&$xml, $position){
		$this->childNodes[$position]=&$xml;
		$xml->parent =& $this;
		$xml->parentPosition = $position;
	}
	function replace_child(&$old, &$new){
		$this->insert_in($new, $old->parentChild);
	}
	function remove_child(&$old){
		$pos = $old->parentPosition;
		$last =count($this->childNodes)-1; 
		for($i=$last ; $i>=$pos; $i--){
			$this->insert_in($this->childNodes[$i], $i-1);
		}
		unset($this->childNodes[$last]);
	}
	function insert_before(&$old, &$new){
		$pos = $old->parentPosition;
		for($i=count($this->childNodes); $i>=$pos; $i--){
			$this->insert_in($this->childNodes[$i-1], $i);
		}
		$this->insert_in($new, $pos);
	}
	
	function setAttribute($name, $val){
		$this->attributes[$name] = $val;
	}
	function setId($id) {
		return $this->id = $id; 
	} 
	function getRealId(){
		if (!$this->controller) backtrace();
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