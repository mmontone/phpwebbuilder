<?php

class Collection extends PWBObject{
	var $elements=null;
	function size(){
		return count($this->elements);
	}
	function &elements(){
		return $this->elements;
	}
	function add(&$elem){
		$es =& $this->elements();
	    $esv[]=&$elem;
	}
	function &pop(){
		$es =& $this->elements();
		$ks = array_keys($es);
		$pos = $ks[count($ks)-1];
		$elem =& $es[$pos];
		unset($es[$this->size()-1]);
		return $elem;
	}
	function &shift(){
		$es =& $this->elements();
		$ks = array_keys($es);
		$pos = $ks[0];
		$elem =& $es[$pos];
		unset($es[$this->size()-1]);
		return $elem;
	}
	function push(&$elem){
		$this->add($elem);
	}
	function &map($func){
		$col =& new Collection;
		$es =& $this->elements();
		$ks = array_keys($es);
		foreach($ks as $k){
			$col->add($func($es[$k]));
		}
		return $col;
	}
	function &collect($mess){
		$col =& new Collection;
		$es =& $this->elements();
		$ks = array_keys($es);
		foreach($ks as $k){
			$col->add(($es[$k]->$mess));
		}
		return $col;
	}
}
?>