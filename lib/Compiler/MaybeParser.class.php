<?php

class MaybeParser extends Parser {
	function MaybeParser(& $parser) {
		parent :: Parser();
		$this->parser = & $parser;
	}
	function setParent(&$parent){
		parent :: setParent($parent);
		$this->parser->setParent($this);
	}
	function parse($tks) {
		$res = $this->parser->parse($tks);
		if ($res[0] === FALSE) {
			return array (
				null,
				$tks
			);
		} else {
			return $res;
		}
	}
	function print_tree() {
		return  '['.
		$this->parser->print_tree().
		']';
	}
	function &process($result) {
		if ($result!=null) return $this->parser->process($result); else {$n=null;return $n;}
	}
	function setError($err){}
}
?>