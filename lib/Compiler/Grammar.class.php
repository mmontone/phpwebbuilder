<?php

class Grammar {
	var $pointcuts = array();
	var $errors = array();
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
		$this->setPointcuts(array_merge($this->pointcuts,$ps));
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
		$root = & new SubParser($this->params['root']);
		$root->setParent($this,$this);
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
		$this->errors = array();
		$this->input = $str;
		$root =& $this->getRoot();
		$this->res = $root->parse(new ParseInput($str));
		if (preg_match('/^[\s\t\n]*$/',$this->res[1]->str)){
			return $root->process($this->res[0]->match);//$this->process($this->params['root'],$res1);
		} else {
			return $this->getError($str);
		}
	}
	function isError(){
		return !empty($this->errors);
	}
	function &getError(){
		$str = $this->input;
		$ret = '';
		$min = strlen($str);
		foreach ($this->errors as $err){
			$remaining=$err['rem']; $symbol=$err['sym'];
			if ($remaining<=$min){
				if ($remaining < $min) $ret='';
				$min = $remaining;
				if ($remaining==0){
					$rem = 'EOF';
					$prev = $str;
				} else {
					$rem = '"'.substr($str, -$remaining, 10). '"';
					$prev = substr($str,0, -$remaining);
				}
				$lines = explode("\n",$prev);
				$nl = count($lines);
				$ret .="\n".'Unexpected '.$rem.', expecting '.$symbol.
				' on line '.$nl. ',character '.(strlen(array_pop($lines))+1);
			}
		}

		return $ret;
	}
	function setError($err){
		$this->errors= array_merge($err,$this->errors);
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