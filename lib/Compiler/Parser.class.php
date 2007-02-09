<?php

require_once dirname(__FILE__).'/PHPCC.class.php';

class Grammar {
	var $pointcuts = array();
	function Grammar($params) {
		$this->params = & $params;
		foreach (array_keys($this->params['nt']) as $k) {
			$this->params['nt'][$k]->setParent($this);
		}
	}
	function &get($name) {
		return $this->params['nt'][$name];
	}
	function addPointCuts($ps){
		$this->pointcuts= array_merge($ps,$this->pointcuts);
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
		$root =& $this->getRoot();
		$res = $root->parse($str);
		if (preg_match('/^[\s\t\n]*$/',$res[1])){
			$res1 =& $root->process($res[0]);
			return $this->process($this->params['root'],$res1);
		} else {
			$n=null;
			var_dump($this->error);
			return $this->error;
		}
	}
	function setError($err){
		$this->error=& $err;
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

class Parser {
	function Parser() {}
	function &get($name) {
		$gr =& $this->getGrammar();
		return $gr->get($name);
	}
	function &getGrammar() {
		return $this->parentParser->getGrammar();
	}
	function setParent(&$parent){
		$this->parentParser =& $parent;
	}
	function &process($result){return $result;}
	function setError($err){
		$this->parentParser->setError($err);
	}
}

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
			if (strcasecmp(get_class($c),'altparser')=='0'){
				$t = '('.$t.')';
			}
			if (is_numeric($k)){
				$ret []= $t;
			} else {
				$ret []= $k.'->'.$t;
			}
		}
		return implode(',',$ret);
	}
}

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

class ListParser extends Parser {
	function ListParser(& $parser, & $separator) {
		parent :: Parser();
		$this->sep = & $separator;
		$this->parser = & $parser;
	}
	function setParent(&$parent){
		parent :: setParent($parent);
		$this->sep->setParent($this);
		$this->parser->setParent($this);
	}
	function parse($tks) {
		/*first, we parse the list*/
		$mp = & new MultiParser(new SeqParser(array (
			$this->parser,
			$this->sep
		)));
		$mp->setParent($this);
		$res = $mp->parse($tks);
		/* then, we parse again, in the tail of the list */
		$res1 = $this->parser->parse($res[1]);
		/* if the tail failed, the parse failed */
		if ($res1[0] === FALSE)
			return array (
				FALSE,
				$tks
			);
		/* we collect the last parsed token, in the first position of the subarray (as all the other ones) */
		$res[0][] = array (
			$res1[0]
		);
		return array (
			$res[0],
			$res1[1]
		);
	}
	function setError($err){
		$this->errorBuffer=& $err;
	}
	function print_tree() {
		return '{'.
		$this->parser->print_tree().
		';'.
		$this->sep->print_tree().
		'}';
	}
	function &process($res) {
		for ($i=0; $i<count($res);$i++){
			$ret []=&$this->parser->process($res[$i][0]);
			if(isset($res[$i][1]))
				$ret []=&$this->sep->process($res[$i][1]);
		}
		return $ret;
	}

}

class MultiParser extends Parser {
	function MultiParser(& $parser) {
		parent :: Parser();
		$this->parser = & $parser;
		$parser->setParent($this);
	}
	function setParent(&$parent){
		parent :: setParent($parent);
		$this->parser->setParent($this);
	}
	function parse($tks) {
		$res = array (
			1,
			$tks
		);
		while ($res[0] !== FALSE) {
			$res = $this->parser->parse($res[1]);
			$ret[] = $res[0];
		}
		array_pop($ret);
		return array (
			$ret,
			$res[1]
		);
	}
	function print_tree() {
		return '('.$this->parser->print_tree(). ')*';
	}
	function &process($res) {
		foreach($res as $r){
			$ret []=&$this->parser->process($r);
		}
		return $ret;
	}
}

class EregSymbol extends Parser {
	function EregSymbol($sym) {
		parent :: Parser();
		$this->sym = $sym;
	}
	function parse($tks) {
		if (preg_match('/^[\s\t\n]*(' . $this->sym . ')[\s\t\n]*/', $tks, $matches)) {
			return array ($matches[1],substr($tks,strlen($matches[0])));
		} else {
			$this->setError('Unexpected "'.$tks[0].'", expecting "'.$this->sym.'" in'.$this->parentParser->print_tree().' with "'.print_r($tks,TRUE). '" remaining');
			return array (FALSE,$tks);
		}
	}
	function print_tree() {
		return '"' . $this->sym . '"';
	}
}

class Symbol extends EregSymbol {
}

class Symbols extends EregSymbol {
	function Symbols($ss) {
		parent :: EregSymbol(implode('|', $ss));
	}
}

class Identifier extends EregSymbol {
	function Identifier() {
		parent :: EregSymbol('[a-zA-Z_][a-zA-Z_0-9]*');
	}
}

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


class FObject {
    function FObject(&$target, $method_name) {
        #@gencheck if(!method_exists($target, $method_name)) { print_backtrace('Method ' . $method_name . ' does not exist in ' . getClass($target));        }//@#
        $this->setTarget($target);
        $this->method_name =& $method_name;
    }
	function setTarget(&$target){
		$this->target =& $target;
	}
	function &getTarget(){
		return $this->target;
	}
    function callString($method) {
    	if ($this->target === null) {
    		return '$ret =& '. $method;
    	}
    	else {
       		return '$t =& $this->getTarget(); $ret =& $t->' . $method;
    	}
    }
    function &callWith(&$params) {
		$method_name = $this->method_name;
		$ret ='';
    	eval($this->callString($method_name) . '($params);');
    	return $ret;
    }
}

//PHP
/*
function parseFunction(){
	SeqParser(array(Symbol('function'),Identifier,Symbol('(')));
}

function functionCall(){
	return new SeqParser(array(new Identifier, new Symbol('\('),new MultiParser(),new Symbol('\)')));
}

$id =& new SeqParser(array(new Identifier, new Symbol('\('),new Identifier, new Symbol('->')));//, new Symbol('->'),new Symbol('\)')));
$id =& new AltParser(array(new Identifier,functionCall()));
print_r($id->compile('algo()'));
*/

?>