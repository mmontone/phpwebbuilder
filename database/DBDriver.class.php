<?php
class DBDriver {
	var $session;
	var $conn;
	var $pconn;
	var $in_transaction = false;
	var $new_request = true;

	function DBDriver(& $session) {
		$this->initialize();
		$this->session = & $session;
		pwb_register_shutdown_function('dbdriver', new FunctionObject($this, 'shutdown'));
	}

	function shutdown() {
		if ($this->inTransaction()) {
			$conn = & $this->openDatabase(false);
			#@sql_dml_echo echo '<div>Rolling back because transaction didn\'t finish in this request</div>';@#
			$this->basicRollback($conn);
			/****Rolling back commands****/
			#@sql_dml_echo echo '<div>Rolling back commands</div>';@#
			$mts =& $this->session->memoryTransactions;
			$count = count($mts);
			for ($i = $count -1; $i >= 0; $i--) {
				$mts[$i]->rebuild();
			}
			$this->session->rebuild();
		}
		$this->new_request = true;
	}

	function inTransaction() {
		return $this->in_transaction;
	}

	function initialize() {
	}

	function batchExec($sqls) {
		foreach ($sqls as $sql) {
			if (trim($sql) != "") { //User Might have included a "" at the end
				$rec = $this->query($sql);
				$ret[] = array (
					$sql,
					$this->fetchArray($rec
				));
			}
		}
		return $ret;
	}
	function fetchArray($res) {
		$arr = array ();
		if ($res === true) {
			$arr[] = "Query suceeded";
		} else
			if (is_exception($res)) {
				$arr[] = "Query failed " . $res->printHtml();
				trigger_error($this->getError(), E_USER_NOTICE);
			} else {
				while ($rec = $this->fetchRecord($res))
					$arr[] = $rec;
			}
		return $arr;
	}
	function & query($sql, $persistent = false) {
		$conn = & $this->openDatabase($persistent);
		$this->setLastSQL($sql);

		$this->processTransactionQueries($conn);
		#@sql_query_echo
		if (substr($sql, 0, 6) == 'SELECT') {
			if (defined('dbgmode')) {
				sql_log(array (
					'Querying: ',
					$sql
				));
			} else {
				echo ('Querying ' . $sql . '<br/>');
			}
		} //@#
		#@sql_dml_echo
		if (substr($sql, 0, 6) != 'SELECT') {
			if (defined('dbgmode')) {
				sql_log(array (
					'DMLing: ',
					$sql
				));
			} else {
				echo ('DMLing ' . $sql . '<br/>');
			}
		} //@#
		#@sql_query_echo2 if (substr($sql,0,6)=='SELECT') {$reg = $this->basicQuery ($conn,'EXPLAIN '.$sql);foreach($this->fetchArray($reg) as $r){if ($r['type']!='eq_ref'){print_r($r); echo '<br/>';}}}@#

		// Before adding a transaction query, we check that it is not a query for reading. Just
		// for performance reasons. We cannot handle dirty reads anyway. There's no magic with relational
		// databases used this way.
		//           --marian

		$reg = $this->basicQuery($conn, $sql);
		if (!$reg) {
			$error = & $this->registerDBError($sql);
			#@sql_dml_echo $lastError =& $this->getLastError(); echo $lastError->printHtml() . '<br />';@#
			return $error->raise();

		}

		return $reg;
	}
	function beginTransaction() {
		$conn = & $this->openDatabase(false);
		/* If transactions are not supported, go on silently (logging is another option)*/
		$this->in_transaction = true;
		$this->basicBeginTransaction($conn);
		#@sql_dml_echo echo 'Beggining DB transaction<br/>';@#
		#@sql_dml_echo2 print_backtrace('Beggining DB transaction');@#
		//trigger_error("Starting transaction");
	}
	function queryDB($query) {
		$res = $this->batchExec(array (
			$query
		));
		return $res[0][1];
	}

	function setLastError(& $error) {
		$this->session->setLastError($error);
	}

	function & getLastError() {
		return $this->session->getLastErr();
	}

	function setLastSQL($sql) {
		$this->session->setLastSQL($sql);
	}

	function getLastSQL() {
		return $this->session->getLastSQL();
	}
	function commit() {
		$conn = & $this->openDatabase(false);
		/* If transactions are not supported, go on silently (logging is another option)*/
		$this->processTransactionQueries($conn);
		$this->in_transaction = false;
		$this->basicCommit($conn);
		#@sql_dml_echo echo 'Committing DB transaction<br/>';@#
		#@sql_dml_echo2 print_backtrace('Committing DB transaction');@#
		//trigger_error("Comitting transaction");
	}

	function processTransactionQueries(& $conn) {
		if ($this->in_transaction) {
			if ($this->new_request) {
				$this->new_request = false;
				$this->beginTransaction();
				$mts = & $this->session->memoryTransactions;
				foreach (array_keys($mts) as $mtk) {
					$mt = & $mts[$mtk];
					#@sql_dml_echo echo 'Running commands stored from previous requests: '.$mt->debugPrintString();@#
					$mt->runCommands($this);
				}
			}
		}
	}

	function rollBack() {
		$conn = & $this->openDatabase(false);
		/* If transactions are not supported, go on silently (logging is another option)*/
		//$this->processTransactionQueries($conn);
		$this->in_transaction = false;
		$this->basicRollback($conn);
		#@sql_dml_echo echo 'Rolling back DB transaction<br/>';@#
		//trigger_error("Rolling back transaction");
	}

	function & registerDBError($sql) {
		trigger_error($this->getError());
		$error = & new DBError(array (
		'number' => mysql_errno(), 'message' => $this->getError(), 'sql' => $sql));
		$this->setLastError($error);
		return $error;
	}
	function & openDatabase($persistent) {
		if ($persistent) {
			if (!$this->pconn) {
				$this->pconn = $this->basicPConnect();
			} else {
				return $this->pconn;
			}
			$con = & $this->pconn;
		} else {
			if (!$this->conn) {
				$this->conn = $this->basicConnect();
			} else {
				return $this->conn;
			}
			$con = & $this->conn;
			$this->pconn = & $this->conn;
		}
		$n = null;
		if (!$con) {
			$this->registerDBError('CONNECT');
			return $n;
		}
		if (!$this->selectDB()) {
			$this->registerDBError('SELECTDB ' . constant('basename'));
			return $n;
		}
		if ($persistent) {
			return $this->pconn;
		} else {
			return $this->conn;
		}
	}
	function tableExists($table) {
		return $this->getRowsAffected($this->query($this->getTableSQL($table)));
	}
	function getTables() {
		return $this->fetchArray($this->query($this->getTablesSQL()));
	}
}
?>