<?php

class DBDriver {
	var $session;
	var $conn;
	var $pconn;
	function DBDriver(&$session) {
		$this->initialize();
		$this->session =& $session;
	}

	function initialize() {}

	function batchExec($sqls) {
		foreach($sqls as $sql) {
			if (trim($sql)!="") { //User Might have included a "" at the end
				$rec = $this->query($sql);
				$ret []= array($sql, $this->fetchArray($rec));
			}
		}
		return $ret;
	}
    function fetchArray($res) {
		$arr = array();
		if ($res===true) {
			$arr[]="Query suceeded";
		} else if (is_exception($res)) {
			$arr[]="Query failed " . $res->printHtml();
			trigger_error($this->getError(),E_USER_NOTICE);
		} else {
			while ($rec = $this->fetchRecord($res)) $arr[]= $rec;
		}
		return $arr;
	}
    function &query($sql, $persistent=false) {
    	#@sql_echo	echo($sql. '<br/>');@#
    	$conn =& $this->openDatabase($persistent);
		$this->setLastSQL($sql);
        #@sql_echo if (substr($sql,0,6)=='SELECT') {$reg = $this->basicQuery ($conn,'EXPLAIN '.$sql);foreach($this->fetchArray($reg) as $r){if ($r['type']!='eq_ref'){print_r($r); echo '<br/>';}}}@#
        $reg = $this->basicQuery($conn,$sql);
        if (!$reg) {
        	$error =& $this->registerDBError($sql);
        	#@sql_echo $lastError =& $this->getLastError(); echo $lastError->printHtml() . '<br />';@#
    		return $error->raise();

        }
        return $reg;
    }
    function beginTransaction() {
    	$conn =& $this->openDatabase(false);
        /* If transactions are not supported, go on silently (logging is another option)*/
        $this->basicbeginTransaction($conn);
        //trigger_error("Starting transaction");
    }
	function queryDB($query){
		$res = $this->batchExec(array($query));
		return $res[0][1];
	}

	function setLastError(&$error) {
		$this->session->setLastError($error);
	}

	function &getLastError() {
		return $this->session->getLastErr();
	}

	function setLastSQL($sql) {
		$this->session->setLastSQL($sql);
	}

	function getLastSQL() {
		return $this->session->getLastSQL();
	}
    function commit() {
        $conn=&$this->openDatabase(false);
        /* If transactions are not supported, go on silently (logging is another option)*/
        $this->basicCommit($conn);
        //trigger_error("Comitting transaction");
    }

    function rollBack() {
        $conn=& $this->openDatabase(false);
        /* If transactions are not supported, go on silently (logging is another option)*/
        $this->basicRollback($conn);
        //trigger_error("Rolling back transaction");
    }
	function &registerDBError($sql) {
    	trigger_error($this->getError());
    	$error =& new DBError(array('number' => mysql_errno(), 'message' => $this->getError(), 'sql' => $sql));
    	$this->setLastError($error);
    	return $error;
    }
    function &openDatabase($persistent) {
    	if ($persistent){
    		if (!$this->pconn){
	      		$this->pconn =& $this->basicPConnect();
    		} else {
    			return $this->pconn;
    		}
    		$con =& $this->pconn;
    	} else {
    		if (!$this->conn){
	      		$this->conn =& $this->basicConnect();
    		} else {
    			return $this->conn;
    		}
    		$con =& $this->conn;
    		$this->pconn =& $this->conn;
    	}
    	$n=null;
	    if (!$con){
	        $this->registerDBError('CONNECT');
	        return $n;
	    }
	    if (!$this->selectDB()){
	        $this->registerDBError('SELECTDB ' . constant('basename'));
	        return $n;
    	}
    	if ($persistent){ return $this->pconn;} else { return $this->conn;}
    }
}

?>