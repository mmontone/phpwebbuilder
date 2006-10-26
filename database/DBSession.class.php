<?php

class DBSession {
	var $current_transaction = null;
	var $driver;
	var $lastError;
	var $lastSQL = '';
	var $rollback_on_error = false;
	//var $rollback = false;
	var $nesting = 0;

	function beginTransaction() {
		if ($this->nesting == 0) {
			$this->driver->beginTransaction();
			$this->current_transaction =& new DBTransaction($this->driver);
		}
		else {
			$this->nesting++;
		}
	}


	function &currentTransaction() {
		return $this->current_transaction;
	}

	function commit() {
		if ($this->nesting == 0) {
			//if (!$this->rollback) {
				$this->current_transaction->commit();
			//}
			$this->expireTransaction();
		}
		else {
			$this->nesting--;
		}

	}

	function rollback() {
		if ($this->nesting == 0) {
			$this->current_transaction->rollback();
			$this->expireTransaction();
		}
		else {
			$this->nesting--;
			//$this->rollback=true;
		}
	}

	function expireTransaction() {
		$n = null;
		$this->current_transaction =& $n;
		//$this->rollback = false;
		if ($this->nesting !== 0) {
			print_backtrace('Error');
		}
	}

	function &Instance(){
		$slot = 'db_session';
		if (!isset($_SESSION[constant('sitename')][$slot])){
			$dbsession_class = 'DBSession';
			if (defined('dbsession_class')) {
				$dbsession_class = constant('dbsession_class');
			}
			$driver_class = constant('db_driver');
			$dbsession =& new $dbsession_class;
			$dbsession->driver =& new $driver_class($dbsession);
			$_SESSION[constant('sitename')][$slot] =& $dbsession;
		}
		return $_SESSION[constant('sitename')][$slot];
	}

	function lastError(){
		$last_error =& DBSession::GetLastError();
		return $last_error->getMessage();
	}

	function &GetLastError() {
		$db =& DBSession::instance();
		return $db->lastError;
	}

	function lastSQL(){
		$db =& DBSession::instance();
		return $db->lastSQL;
	}

	function SQLExec($sql, $getID=false, $obj=null, $rows=0) {
       	return $this->driver->SQLExec($sql, $getID, & $obj, & $rows);
    }

    function queryDB($query){
    	return $this->driver->queryDB($query);
    }

    function setLastError(&$error) {
		$this->lastError =& $error;
	}

	function &getLastErr() {
		return $this->lastError;
	}

	function setLastSQL($sql) {
		$this->lastSQL = $sql;
	}

	function getLastSQL() {
		return $this->lastSQL;
	}

	function fetchrecord($res) {
    	return $this->driver->fetchrecord($res);
    }

    function openDatabase() {
    	return $this->driver->openDatabase();
    }
    function closeDatabase() {
      return $this->driver->closeDatabase();
    }

    function fetchArray($res) {
		return $this->driver->fetchArray($res);
	}

	function query($q) {
		return $this->driver->query($q);
	}

	function batchExec($sqls) {
		return $this->driver->batchExec($sqls);
	}

	function &save(&$object) {
		$res =& $object->save();
		if ($this->isException($res)) {
			if ($this->rollback_on_error) {
				$this->rollback();
			}
		}
		else {
			$this->current_transaction->save($object);
		}
		return $res;
	}

	function isException(&$ex) {
		return is_exception($ex);
	}

	function delete(&$object) {
		$res =& $object->delete();
		if ($this->isException($res)) {
			if ($this->rollback_on_error) {
				$this->rollback();
			}
		}
		else {
			$this->current_transaction->delete($object);
		}
		return $res;
	}

	function rollbackOnError($b = true) {
		$this->rollback_on_error = $b;
	}
}



class DBError {
	var $number;
	var $message;
	var $sql;

	function DBError($params) {
		$this->number = $params['number'];
		$this->message = $params['message'];
		$this->sql = $params['sql'];
	}

	function getNumber() {
		return $this->number;
	}

	function getMessage() {
		return $this->message;
	}

	function getSQL() {
		return $this->sql;
	}

	function printHtml() {
		return 'DBError: <br/>Number: ' . $this->getNumber() . '<br />Message: ' . $this->getMessage() . '<br />SQL: ' . $this->getSQL();
	}
}

?>