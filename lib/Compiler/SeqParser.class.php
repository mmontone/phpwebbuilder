<?php

class SeqParser extends Parser {
	function SeqParser($children) {
		if (!is_array($children)) {
			echo 'NOT ARRAY!';
			print_r($children);
			exit;
		}
		parent :: Parser();
		$this->children =& $children;
	}
	function setParent(&$parent){
		parent :: setParent($parent);
		foreach (array_keys($this->children) as $k) {
			$this->children[$k]->setParent($this);
		}
	}

	function &process($result) {
		foreach (array_keys($this->children) as $k) {
			$rets [$k]=&$this->children[$k]->process($result[$k]);
		}
		return $rets;
	}
	function parse($tks) {
		$res = array (FALSE,$tks);
		foreach (array_keys($this->children) as $k) {
			$res = $this->children[$k]->parse($res[1]);
			$ret[$k] = $res[0];
			if ($res[0] === FALSE ) {
				return array (FALSE,$tks);
			}
		}

		return array ($ret,$res[1]);
	}
	function print_tree() {
		foreach (array_keys($this->children) as $k) {
			$c = & $this->children[$k];
			$t = $c->print_tree();
			if (getClass($c)=='altparser'){
				$t = '('.$t.')';
			}
			if (is_numeric($k)){
				$ret []= $t;
			} else {
				$ret []= $k.'->'.$t;
			}
		}
		return implode(' ',$ret);
	}
}
?>