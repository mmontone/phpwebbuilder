<?php

class Collection extends PWBObject{
	var $elements=array();
	function size(){
		return count($this->elements);
	}
	function &at($pos){
		$es =& $this->elements();
	    return $es[$pos];
	}
	function indexOf(&$elem){
		$es =& $this->elements();
		$ks = array_keys($es);
		if (isPWBObject($elem)){
			$f = lambda('&$e1','return $elem->is($e1);',get_defined_vars());
		} else {
			$f = lambda('&$e1','return $elem==$e1;',get_defined_vars());
		}
		foreach($ks as $k){
			$e =& $es[$k];
			if ($f($e)){
				return $k;
			}
		}
		return -1;
	}
	function &elements(){
		return $this->elements;
	}
	function add(&$elem){
		$es =& $this->elements();
	    $es[]=&$elem;
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
		$f = lambda('&$e', 'return $e->$mess();', get_defined_vars());
		return $this->map($f);
	}
}
?>