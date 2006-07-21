<?php

class Collection extends PWBObject{
	var $elements=array();
	function size(){
		return count($this->elements);
	}
	function isEmpty(){
		return $this->size()==0;
	}
	function &first(){
		return $this->at(0);
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
		delete_lambda($f);
		return -1;
	}
	function includes(&$elem){
		return $this->indexOf($elem) != -1;
	}
	function &elements(){
		return $this->elements;
	}
	function add(&$elem){
		$es =& $this->elements();
	    $es[]=&$elem;
	    $this->triggerEvent('changed', $elem);
	}
	function &pop(){
		$es =& $this->elements();
		$ks = array_keys($es);
		$pos = $ks[count($ks)-1];
		$elem =& $es[$pos];
		unset($es[$this->size()-1]);
		$this->triggerEvent('changed', $elem);
		return $elem;
	}
	function &shift(){
		$es =& $this->elements();
		$ks = array_keys($es);
		$pos = $ks[0];
		$elem =& $es[$pos];
		unset($es[$this->size()-1]);
		$this->triggerEvent('changed', $elem);
		return $elem;
	}
	function push(&$elem){
		$this->add($elem);
	}
	function &map($func){
		$res =& $this->foldr(new Collection, $f = lambda('&$col,&$elem',
			'$col->add($func($elem)); return $col;', get_defined_vars()));
		delete_lambda($f);
		return $res;

	}
	function &filter($pred){
		$res =& $this->foldr(new Collection, $f = lambda('&$col,&$elem',
			'if ($pred($elem)) $col->add($elem); return $col;', get_defined_vars()));
		delete_lambda($f);
		return $res;
	}
	function &foldr(&$z, $f){
		$acc =& $z;
		$es =& $this->elements();
		$ks = array_keys($es);
		foreach($ks as $k){
			$acc = $f($acc, $es[$k]);
		}
		return $acc;
	}
	function &collect($mess){
		$res =& $this->map(
				$f = lambda('&$e', 'return apply_messages($e,$mess);', get_defined_vars())
			);
		delete_lambda($f);
		return $res;
	}
	function &toArray(){
		return $this->elements();
	}
	function addAll($arr){
		$ks = array_keys($arr);
		foreach($ks as $k){
			$this->add($arr[$k]);
		}
	}
	function concat(&$col){
		$this->addAll($col->elements());
	}
	function getDataType(){
		return '';
	}
	function refresh(){}
}
?>