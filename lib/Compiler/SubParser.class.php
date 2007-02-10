<?php

class SubParser extends Parser {
	function SubParser($name) {
		parent :: Parser();
		$this->subName = $name;
	}
	function parse($tks) {
		$p = & $this->get($this->subName);
		$res = $p->parse($tks);
		return $res;
	}
	function print_tree() {
		return '<' . $this->subName . '>';
	}
	function &process($res){
		$p = & $this->get($this->subName);
		$ret =& $p->process($res);
		$g =& $this->getGrammar();
		return $g->process($this->subName, $ret);
	}
}

?>