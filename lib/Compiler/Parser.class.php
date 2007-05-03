<?php

class Parser {
	function Parser() {}
	function &get($name) {
		$gr =& $this->getGrammar();
		return $gr->get($name);
	}
	function &getGrammar() {
		return $this->grammar;
	}
	function setParent(&$parent, &$grammar){
		$this->grammar =& $grammar;
		$this->setErrorHandler($parent);
	}
	function setErrorHandler(&$eh){
		$this->errorHandler[] =& $eh;
	}
	function &popErrorHandler(){
		$lastpos = $this->lastErrorHandlerPos();
		$eh =& $this->errorHandler[$lastpos];
		unset($this->errorHandler[$lastpos]);
		return $eh;
	}
	function lastErrorHandlerPos(){
		$poss = array_keys($this->errorHandler);
		return $poss[count($poss)-1];
	}
	function &process($result){return $result;}
	function setError($err){
		/*echo "<br/>setting $err from ".get_class($this) . " to ".get_class($this->errorHandler[count($this->errorHandler)-1]);*/
		if (!isset($this->errorHandler[$this->lastErrorHandlerPos()]))print_r($this->errorHandler);
		$this->errorHandler[$this->lastErrorHandlerPos()]->setError($err);
	}
}

class ParseInput{
	var $partials = array();
	var $nts=array();
	function ParseInput($str){
		$this->str = $str;
	}
	function addPartial($name, $res){
		$this->partials[$name]=$res;
	}
	function getPartial($name){
		return @$this->partials[$name];
	}
	function pushNonTerminal($nt){
		array_push($this->nts,$nt);
	}
	function popNonTerminal($nt){
		array_pop($this->nts);
	}
	function includesNonTerminal($nt){
		return in_array($nt,$this->nts);
	}
	function redescendNonTerminal($nt){
		$this->rdnts[$nt] = $nt;
	}
	function shouldReDescend($nt){
		$b = isset($this->rdnts[$nt]);
		/*unset($this->rdnts[$nt]);*/
		return $b;
	}
	function isBetterMatchThan($input){
		return $input->str===null || (strlen($this->str) < strlen($input->str));
	}
}

class ParseResult{
	var $match;
	function fail(){
		/*return ParseResult::match(FALSE);*/
		$pr = new ParseResult();
		$pr->failed=true;
		return $pr;
	}
	function match($result){
		$pr = new ParseResult();
		$pr->match=$result;
		return $pr;
	}
	function lambda(){
		$pr = new ParseResult();
		$pr->lambda=true;
		$pr->match=null;
		return $pr;
	}
	function isLambda(){
		return isset($this->lambda);
	}
	function failed(){
		return isset($this->failed);
	}
}

compile_once(dirname(__FILE__).'/AltParser.class.php');
compile_once(dirname(__FILE__).'/Grammar.class.php');
compile_once(dirname(__FILE__).'/ListParser.class.php');
compile_once(dirname(__FILE__).'/MaybeParser.class.php');
compile_once(dirname(__FILE__).'/MultiParser.class.php');
compile_once(dirname(__FILE__).'/SeqParser.class.php');
compile_once(dirname(__FILE__).'/SubParser.class.php');
compile_once(dirname(__FILE__).'/Symbol.class.php');

?>