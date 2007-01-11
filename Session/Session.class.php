<?php

class Session extends PersistentObject{
	function initialize(){
		$this->addField(new TextField('session_name', TRUE));
		$this->addField(new TextField('session_id', TRUE));
		$this->addField(new DateTimeField('date_created', TRUE));
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
	function removeAttribute($name){
		$s =& Session::get();
		unset($s[$name]);
	}
	function &get(){
		return $_SESSION;
	}
}

?>