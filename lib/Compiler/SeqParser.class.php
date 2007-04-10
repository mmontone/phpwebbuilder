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
	function setParent(&$parent, &$grammar){
		parent :: setParent($parent, $grammar);
		foreach (array_keys($this->children) as $k) {
			$this->children[$k]->setParent($this, $grammar);
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
		$ret = array();
		#@parse_echo
		$mi_parse_id = @$GLOBALS['parse_id']++;
		echo '<li>';
		echo '<a onclick="hideshowchild(event.target);">';
		htmlshow('entering sequence: '.$this->print_tree());
		echo '</a>';
		echo '<ul style="visibility:hidden;width:0;height:0;">';
		//@#
		foreach (array_keys($this->children) as $k) {
			$res = $this->children[$k]->parse($res[1]);
			if ($res[0]->failed()) {
				#@parse_echo
				htmlshow($this->print_tree(). 'failed');
				var_dump($ret);
				echo '</li>';
				echo '</ul>';
				//@#
				return array (ParseResult::fail(),$tks);
			}
			$ret[$k] = $res[0]->match;
		}
		#@parse_echo
		htmlshow($this->print_tree(). 'passed');
		var_dump($ret);
		echo '</li>';
		echo '</ul>';
		//@#
		return array (ParseResult::match($ret),$res[1]);
	}
	function print_tree() {
		foreach (array_keys($this->children) as $k) {
			$c = & $this->children[$k];
			$t = $c->print_tree();
			if (strtolower(get_class($c))=='altparser'){
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