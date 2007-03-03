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
	function popErrorHandler(){
		array_pop($this->errorHandler);
	}
	function &process($result){return $result;}
	function setError($err){
		$this->errorHandler[count($this->errorHandler)-1]->setError($err);
	}
}

require_once 'AltParser.class.php';
require_once 'Grammar.class.php';
require_once 'ListParser.class.php';
require_once 'MaybeParser.class.php';
require_once 'MultiParser.class.php';
require_once 'SeqParser.class.php';
require_once 'SubParser.class.php';
require_once 'Symbol.class.php';

?>