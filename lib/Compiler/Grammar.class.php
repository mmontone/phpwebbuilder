<?php

class Grammar {
	var $pointcuts = array();
	var $error;
	function Grammar($params) {
		$this->params = & $params;
		foreach (array_keys($this->params['nt']) as $k) {
			$this->params['nt'][$k]->setParent($this, $this);
		}
	}
	function &get($name) {
		return $this->params['nt'][$name];
	}
	function addPointCuts($ps){
		$this->setPointcuts(array_merge($ps,$this->pointcuts));
	}
	function setPointCuts($ps){
		$this->pointcuts=$ps;
	}
	function &getGrammar() {
		return $this;
	}
	function &getProcessor($name) {
		return $this->pointcuts[$name];
	}
	function &getRoot() {
		$root = & $this->get($this->params['root']);
		return $root;
	}
	function &process($name, &$data){
		$p =& $this->getProcessor($name);
		if ($p===null){
			return $data;
		} else {
			return $p->callWith($data);
		}
	}
	function &compile($str) {
		$this->error = null;
		$root =& $this->getRoot();
		$res = $root->parse($str);
		if (preg_match('/^[\s\t\n]*$/',$res[1])){
			$res1 =& $root->process($res[0]);
			return $this->process($this->params['root'],$res1);
		} else {
			return  $this->error;
		}
	}
	function isError(){
		return $this->error !== null;
	}
	function getError(){
		return $this->error;
	}
	function setError($err){
		$this->error= $err;
	}
	function print_tree() {
		$ret =  "<".$this->params['root']."(\n   ";
		foreach (array_keys($this->params['nt']) as $k) {
			$ret.= $k . '::='.
				$this->params['nt'][$k]->print_tree().
				".\n   ";
		}
		return $ret . ")>";
	}
}
?>