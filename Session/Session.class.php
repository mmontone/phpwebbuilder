<?php

#@preprocessor Compiler::usesClass(__FILE__, 'SessionHandler');@#

class Session extends PersistentObject{
	function initialize(){
		$this->addField(new TextField('session_name', TRUE));
		$this->addField(new TextField('session_id', TRUE));
		$this->addField(new DateTimeField('date_created', FALSE));
		$this->addField(new DateTimeField('last_updated', FALSE));
		$this->addField(new BlobField('session_data', FALSE));
	}


	function &getAttribute($name){
		$s =& Session::get();
		return $s[$name];
	}
	function isSetAttribute($name){
		$s =& Session::get();
		return isset($s[$name]);
	}
	function restart(){
		$_SESSION = array();
	}
	function setAttribute($name, &$value){
		$s =& Session::get();
		return $s[$name] =& $value;
	}

    function setAttributeIfNotSet($name, &$value) {
        $s =& Session::get();
        if (!isset($s[$name])) {
        	$s[$name] =& $value;
        }
    }

    function &getAttributeOrSet($name, &$value) {
    	$s =& Session::get();
        if (!isset($s[$name])){
        	$s[$name] =& $value;
        }
        return $s[$name];
    }

	function removeAttribute($name){
		$s =& Session::get();
		unset($s[$name]);
	}

    function &get() {
		if (!isset($_SESSION)) {
        	session_start();
        }
		return $_SESSION;
	}

    function isStarted(){
		$sh =& SessionHandler::Instance();
		return $sh!==null && $sh->isStarted();
	}
}

?>