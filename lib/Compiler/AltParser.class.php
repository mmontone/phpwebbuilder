<?php


class AltParser extends Parser {
	function AltParser($children) {
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
	function parse($tks) {
		foreach (array_keys($this->children) as $k) {
			$c = & $this->children[$k];
			$res = $c->parse($tks);
			if ($res[0] !== FALSE) {
				$res[0]= array($k,$res[0]);
				return $res;
			}
		}
		$this->parentParser->setError($this->errorBuffer);
		return array(FALSE, $tks);
	}
	function print_tree() {
		foreach (array_keys($this->children) as $k) {
			$c = & $this->children[$k];
			if (is_numeric($k)){
				$ret []= $c->print_tree();
			} else {
				$ret []= $k.'=>'.$c->print_tree();
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
		$this->errorBuffer=& $err;
	}
}

?>