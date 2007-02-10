<?php

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

require_once 'AltParser.class.php';
require_once 'Grammar.class.php';
require_once 'ListParser.class.php';
require_once 'MaybeParser.class.php';
require_once 'MultiParser.class.php';
require_once 'SeqParser.class.php';
require_once 'SubParser.class.php';
require_once 'Symbol.class.php';

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