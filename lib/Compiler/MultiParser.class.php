<?php


class MultiParser extends Parser {
	function MultiParser(& $parser) {
		parent :: Parser();
		$this->parser = & $parser;
	}
	function setParent(&$parent, &$grammar){
		parent :: setParent($parent, $grammar);
		$this->parser->setParent($this, $grammar);
	}
	function parse($tks) {
		$res = array (1,$tks);
		while ($res[0] !== FALSE) {
			$res = $this->parser->parse($res[1]);
			$ret[] = $res[0];
		}
		array_pop($ret);
		return array ($ret,	$res[1]);
	}
	function print_tree() {
		return $this->parser->print_tree(). '*';
	}
	function &process($res) {
		foreach($res as $r){
			$ret []=&$this->parser->process($r);
		}
		return $ret;
	}
}

class MultiOneParser extends MultiParser{
	function parse($tks) {
		$res = parent::parse($tks);
		if (count($res[0])==0){
			return array(FALSE, $tks);
		} else {
			return $res;
		}
	}
	function print_tree() {
		return $this->parser->print_tree(). '+';
	}
}

?>