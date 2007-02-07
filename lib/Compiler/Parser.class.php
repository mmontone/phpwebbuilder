<?php

class Grammar {
	function Grammar($params) {
		$this->params = & $params;
		foreach (array_keys($this->params['nt']) as $k) {
			$this->params['nt'][$k][0]->setParent($this);
		}
	}
	function &get($name) {
		return $this->params['nt'][$name][0];
	}
	function &getGrammar() {
		return $this;
	}
	function &getProcessor($name) {
		return $this->params['nt'][$name][1];
	}
	function &getRoot() {
		$root = & $this->get($this->params['root']);
		return $root;
	}
	//tokenize::str->[token]
	function tokenize($str) {
		$pregs = $this->tokenizer();
		$pregs = array_unique($pregs);
		rsort($pregs);
		$expr = '/(' . implode('|', $pregs) . '|[^\s])/';
		preg_match_all($expr, $str, $matches);
		//print_r($matches[0]);
		return $matches[0];
	}
	function tokenizer() {
		$e = array ();
		foreach (array_keys($this->params['nt']) as $k) {
			$tk =$this->params['nt'][$k][0]->tokenizer();
			$e = array_merge($e, $tk);
		}
		return $e;
	}
	function &compile($str) {
		$root =& $this->getRoot();
		$res = $root->parse($this->tokenize($str));
		//print_r($res[1]);
		if (count($res[1])==0){
			$res1 = $root->process($res[0]);
			$proc =& $this->getProcessor($this->params['root']);
			return $proc->callWith($res1);
		} else {
			return $n=null;
		}
	}

	function print_tree() {
		echo "<(\n   ";
		foreach (array_keys($this->params['nt']) as $k) {
			echo $k . '=>';
			$c = & $this->params['nt'][$k][0]->print_tree();
			echo ",\n   ";
		}
		echo ")\n," . $this->params['root'] . ">\n";
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
	function process($result){return $result;}
	//tokenizer::[preg]
	//parse::[token]->(FALSE|<result>,[token])
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
	function tokenizer() {
		$e = array ();
		foreach (array_keys($this->children) as $k) {
			$tk = $this->children[$k]->tokenizer();
			$e = array_merge($e, $tk);
		}
		return $e;
	}
	//parse(while(fst.children==null)),
	function parse($tks) {
		foreach (array_keys($this->children) as $k) {
			$c = & $this->children[$k];
			$res = $c->parse($tks);
			if ($res[0] !== FALSE) {
				$ret[0]= array($k,$res[0]);
				$ret[1]=$res[1];
				return $ret;
			}
		}
		return array(FALSE, $tks);
	}
	function print_tree() {
		foreach (array_keys($this->children) as $k) {
			$c = & $this->children[$k];
			$c->print_tree();
			echo '|';
		}
	}
	function &process($result) {
		$rets =&$this->children[$result[0]]->process($result[1]);
		return array('selector'=>$result[0],'result'=>$rets);
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

	function process($result) {
		foreach (array_keys($this->children) as $k) {
			$rets [$k]=&$this->children[$k]->process($result[$k]);
		}
		return $rets;
	}
	//tokenizer::preg::concat(children::[preg])
	function tokenizer() {
		$e = array ();
		foreach (array_keys($this->children) as $k) {
			$tk = $this->children[$k]->tokenizer();
			$e = array_merge($e, $tk);
		}
		return $e;
	}
	//parse(while(fst.children==null)),
	function parse($tks) {
		$res = array (FALSE,$tks);
		foreach (array_keys($this->children) as $k) {
			$res = $this->children[$k]->parse($res[1]);
			$ret[$k] = $res[0];
			if ($res[0] === FALSE) {
				//echo 'Unexpected "'.$res[1][0]. '"';
				return array (FALSE,$tks);
			}
		}
		return array ($ret,$res[1]);
	}
	function print_tree() {
		foreach (array_keys($this->children) as $k) {
			$c = & $this->children[$k];
			$c->print_tree();
			echo ',';
		}
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
	//tokenizer::preg
	function tokenizer() {
		return $this->parser->tokenizer();
	}
	//parse(while(fst.children==null)),
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
		echo '(';
		$this->parser->print_tree();
		echo ')?';
	}
	function &process($result) {
		if ($result!=null) return $this->parser->process($result); else {return $n=null;}
	}
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

	function tokenizer() {
		$tk = $this->sep->tokenizer();
		$e = $tk;
		$tk = $this->parser->tokenizer();
		$e = array_merge($e, $tk);
		return $e;
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
	function print_tree() {
		$this->sep->print_tree();
		$this->parser->print_tree();
	}
	function &process($res) {
		for ($i=0; $i<count($res);$i+=2){
			$ret []=$this->parser->process($res[$i]);
			if(isset($res[$i+1]))
				$ret []=$this->sep->process($res[$i+1]);
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
	//tokenizer::preg
	function tokenizer() {
		return $this->parser->tokenizer();
	}
	//parse(while(fst.children==null)),
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
		$this->parser->print_tree();
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
	function tokenizer() {
		return array ($this->sym);
	}
	function parse($tks) {
		if (preg_match('/^' . $this->sym . '$/', $tks[0])) {
			$res2 = $tks;
			$res = array_shift($res2);
			return array ($res,$res2);
		} else {
			return array (FALSE,$tks);
		}
	}
	function print_tree() {
		echo '"' . $this->sym . '"';
	}
}

class Symbol extends EregSymbol {
}

class Symbols extends EregSymbol {
	function Symbols($ss) {
		parent :: EregSymbol(implode('|', $ss));
		$this->ss = $ss;
	}
	function tokenizer() {
		return $this->ss;
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
	function tokenizer() {
		return array ();
	}
	function parse($tks) {
		$p = & $this->get($this->subName);
		return $p->parse($tks);
	}
	function print_tree() {
		echo '<' . $this->subName . '>';
	}
	function &process($res){
		$g =& $this->getGrammar();
		$proc = $g->getProcessor($this->subName);
		$p = & $this->get($this->subName);
		$ret =& $p->process($res);
		return $proc->callWith($ret);
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

//OQL

//select query:
?>