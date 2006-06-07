<?php

class App
{
	var $db;
}

class Session {
	var $session;
    var $db;
	var $request;

    function Session() {
    	$this->session = array();
    	$this->initialize();
    }

	function initialize() {
		$this->db =& new mysqldb;
		$this->request =& new Request($_REQUEST);
	}

    function &instance() {
    	if ($_SESSION['session'] == null) {
    		session_start();
    		if ($_SESSION['session'] == null)
    			$_SESSION['session'] =& new Session;
    	}
    	return $_SESSION['session'];
    }

    function set($attr, &$value) {
    	$this->session[$attr] = &$value;
    }

    function &get($attr) {
    	return $this->session[$attr];
    }

    function start() {
    	$this->openDatabase();
    }

    function stop() {
    	$this->closeDatabase();
    }

    function sendAnswer($answer) {
    	echo $answer;
    	$this->stop();
    }

    function &getDB() {
    	return $this->db;
    }

    function &request() {
    	return $this->request;
    }

}

class Request {
	var $request;

	function Request(&$request) {
		$this->request =& $request;
	}

	function &instance() {
		$s =& Session::instance();
		return $s->request;
	}
}


?>