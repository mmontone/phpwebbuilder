<?php

/*
 * To find alternatives correctly, we should use a set of possible next symbols, and possible
 * next subparsers (to DFS).
 * If none of the symbols work, fail.
 * If one of them works, and it's only one, that's our alternative. Complete the parse in that branch.
 * If more than one work, ask for the following symbols on each alternative, and proceed again.
 *
 */


class AltParser extends Parser {
	function AltParser($children, $backtrack=false) {
		if (!is_array($children)) {
			echo 'NOT ARRAY!';
			print_r($children);
			exit;
		}
		parent :: Parser();
		$this->backtrack = $backtrack;
		$this->children =& $children;
	}
	function setParent(&$parent, &$grammar){
		parent :: setParent($parent, $grammar);
		foreach (array_keys($this->children) as $k) {
			$this->children[$k]->setParent($this, $grammar);
		}
	}
	function parse($tks) {
		foreach (array_keys($this->children) as $k) {
			$c = & $this->children[$k];
			$res = $c->parse($tks);
			if ($res[0] !== FALSE
				&& (!$this->backtrack || $res[0] !== null)
				) {
				//if ($res[0]==null) print_backtrace(getClass($c). ' '.htmlentities($this->print_tree()));
				$res[0]= array($k,$res[0]);
				return $res;
			}
		}
		parent::setError(implode('',$this->errorBuffer));
		return array(FALSE, $tks);
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
				$ret []= $k.'=>'.$t;
			}
		 }
		 return implode('|',$ret);
	}
	function &process($result) {
		if (!$this->children[$result[0]]) {echo 'wrong alternative:';var_dump($result);}
		$rets =&$this->children[$result[0]]->process($result[1]);
		 $arr = array('selector'=>$result[0],'result'=>$rets);
		return $arr;
	}
	function setError($err){
		$this->errorBuffer[]=& $err;
	}
}

?>