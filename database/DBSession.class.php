<?php

class DBSession {
	var $current_transaction = null;
	var $driver;
	var $lastError;
	var $lastSQL = '';
	var $rollback_on_error = false;
	var $rollback = false;
	var $nesting = 0;

	function beginTransaction() {
		if ($this->nesting == 0) {
			$this->driver->beginTransaction();
			$this->current_transaction =& new DBTransaction($this->driver);
		}

		if (defined('sql_echo') and constant('sql_echo') == 1) {
			echo 'Beggining transaction ('. $this->nesting . ')<br/>';
		}

		$this->nesting++;
	}

	function &currentTransaction() {
		return $this->current_transaction;
	}

	function commit() {
		if ($this->nesting == 1) {
			if (!$this->rollback) {
				$this->current_transaction->commit();
			}
			else {
				echo 'Rolling back!!';
				$this->current_transaction->rollback();
			}

			if (defined('sql_echo') and constant('sql_echo') == 1) {
				echo 'Expiring transaction<br/>';
			}
			$this->expireTransaction();
		}

		$this->nesting--;


		if (defined('sql_echo') and constant('sql_echo') == 1) {
			echo 'Commiting transaction ('. $this->nesting . ')<br/>';
		}
	}

	function rollback() {
		if ($this->nesting == 1) {
			$this->current_transaction->rollback();
			$this->expireTransaction();
		}
		else {
			//echo 'Setting rollback in true';
			$this->rollback=true;
		}

		$this->nesting--;



		if (defined('sql_echo') and constant('sql_echo') == 1) {
			echo 'Rolling back transaction ('. $this->nesting . ')<br/>';
		}
	}

	function expireTransaction() {
		$n = null;
		$this->current_transaction =& $n;
		//echo 'Setting rollback in false';
		$this->rollback = false;
		if ($this->nesting !== 1) {
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

	function &SQLExec($sql, $getID=false, $obj=null, $rows=0) {
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
		$this->current_transaction->save($object);
		$res =& $object->save();
		if (is_exception($res)) {
			if ($this->rollback_on_error) {
				$this->rollback();
			}
		}

		return $res;
	}

	function delete(&$object) {
		$this->current_transaction->delete($object);

		$res =& $object->delete();
		if (is_exception($res)) {
			if ($this->rollback_on_error) {
				$this->rollback();
			}
		}
	}

	function rollbackOnError($b = true) {
		$this->rollback_on_error = $b;
	}
}



class DBError extends PWBException {
	var $number;
	var $sql;

	function createInstance($params) {
		$this->number = $params['number'];
		$this->sql = $params['sql'];
		parent::createInstance($params);
	}

	function getNumber() {
		return $this->number;
	}

	function getSQL() {
		return $this->sql;
	}

	function printHtml() {
		return 'DBError: <br/>Number: ' . $this->getNumber() . '<br />Message: ' . $this->getMessage() . '<br />SQL: ' . $this->getSQL();
	}
}

?>