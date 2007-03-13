<?

class MySQLDriver extends DBDriver {
	var $conn;
	var $pconn;
	var $tables_type = 'MyISAM';

    #@php4
    function &SQLExec ($sql, $getID=false, $obj=null, &$rows) {
       	$rows=0;
       	$this->setLastSQL($sql);
        $reg =& $this->query ($sql);
        if (!is_exception($reg)) {
	        if ($getID) { $obj->setID(mysql_insert_id());};
	        $rows = mysql_affected_rows();
	        return $reg;
        }
        else {
        	$reg->setTargetObject($obj);
            return $reg->raise();
        }
    }//@#

    #@php5
    function &SQLExec ($sql, $getID=false, $obj=null, &$rows) {
        $rows=0;
        $this->setLastSQL($sql);
        try {
            $reg = &$this->query ($sql);
            if ($getID) { $obj->setID(mysql_insert_id());};
            $rows = mysql_affected_rows();
            return $reg;
        } catch (DBError $ex) {
        	$ex->setTargetObject($obj);
            return $ex->raise();
        }
    }//@#

    function initialize() {
		if (defined('tables_type')) {
			$this->setTablesType(tables_type);
		}
    }

    function fetchArray($res) {
		$arr = array();
		if ($res===true) {
			$arr[]="Query suceeded";
		} else if (is_exception($res)) {
			$arr[]="Query failed " . $res->printHtml();
			trigger_error(mysql_error(),E_USER_NOTICE);
		} else {
			while ($rec = $this->fetchRecord($res)) $arr[]= $rec;
		}
		return $arr;
	}

    function beginTransaction() {
    	$this->openDatabase(false);
        /* If transactions are not supported, go on silently (logging is another option)*/
        mysql_query ("START TRANSACTION;"); /* or
            die (print_backtrace(mysql_error() . ": $sql"));*/
        //trigger_error("Starting transaction");
        $this->closeDatabase();
    }

    function commit() {
        $this->openDatabase(false);
        /* If transactions are not supported, go on silently (logging is another option)*/
        mysql_query ("COMMIT;"); /*or
            die (print_backtrace(mysql_error() . ": $sql"));*/
        //trigger_error("Comitting transaction");
        $this->closeDatabase();
    }

    function rollBack() {
        $this->openDatabase(false);
        /* If transactions are not supported, go on silently (logging is another option)*/
        mysql_query ("ROLLBACK;"); /*or
            die (print_backtrace(mysql_error() . ": $sql"));*/
        //trigger_error("Rolling back transaction");
        $this->closeDatabase();
    }

    function &query($sql, $persistent=false) {
    	#@sql_echo	echo($sql. '<br/>');@#
    	$this->openDatabase($persistent);
		$this->setLastSQL($sql);
        #@sql_echo if (substr($sql,0,6)=='SELECT') {$reg = mysql_query ('EXPLAIN '.$sql);foreach($this->fetchArray($reg) as $r){if ($r['type']!='eq_ref'){print_r($r); echo '<br/>';}}}@#
        $reg = mysql_query ($sql);
        $this->closeDatabase();
        if (!$reg) {
        	$error =& $this->registerDBError($sql);
        	#@sql_echo $lastError =& $this->getLastError(); echo $lastError->printHtml() . '<br />';@#
    		return $error->raise();

        }
        return $reg;
    }

    function &registerDBError($sql) {
    	trigger_error(mysql_error());
    	$error =& new DBError(array('number' => mysql_errno(), 'message' => mysql_error(), 'sql' => $sql));
    	$this->setLastError($error);
    	return $error;
    }

    function fetchrecord($res) {
    	return @mysql_fetch_assoc($res);
    }
    function openDatabase($persistent) {
    	if ($persistent){
    		if (!$this->pconn){
	      		$this->pconn = mysql_pconnect(constant('serverhost'), constant('baseuser'), constant('basepass'));
    		} else {
    			return;
    		}
    		$con =& $this->pconn;
    	} else {
    		if (!$this->conn){
	      		$this->conn = mysql_connect(constant('serverhost'), constant('baseuser'), constant('basepass'));
    		} else {
    			return;
    		}
    		$con =& $this->conn;
    		$this->pconn =& $this->conn;
    	}
	    if (!$con){
	        $this->registerDBError('CONNECT');
	        return false;
	    }
	    $b = mysql_select_db(constant('basename'));
	    if (!$b){
	        $this->registerDBError('SELECTDB ' . constant('basename'));
	        return false;
    	}
    	return true;
    }
    function closeDatabase() {
      //mysql_close($this->conn);
      //unset($this->conn);
    }
    function escape($str) {
    	$ret = mysql_real_escape_string($str);
    	return $ret;
    }
    function unescape($str) {
    	$ret = ereg_replace("\\\'","\'",$str);
    	return $ret;
    }

    // SQL

	function showColumnsFromTableSQL($table) {
		return "SHOW COLUMNS FROM `" . $table."`";
	}

	function dropColumnSQL($column) {
		 return "DROP COLUMN `$column`";
	}

	function tablePropertiesSQL() {
		return 'TYPE = ' . $this->getTablesType();
		/*
		switch($this->getMySQLVersionPrimaryNumber()) {
			case 4: return 'TYPE=' . $this->getTablesType();
			case 5: return 'ENGINE=' . $this->getTablesType();
			default: return '';
		}*/
	}

	function setTablesType($type) {
		$this->tables_type =& $type;
	}

	function getTablesType() {
		return $this->tables_type;
	}

	function setMyISAM() {
		$this->tables_type = 'MyISAM';
	}

	function setInnoDB() {
		$this->tables_type = 'InnoDB';
	}

	function getMySQLVersionkPrimaryNumber() {
		$version = $this->getMySQLVersion();
		return $version['primary'];
	}

	function getMySQLVersion() {
		$this->openDatabase(true);
		$res = $this->query('SELECT VERSION();');
		$ver = mysql_result($res, 0);
		$this->closeDatabase();
		preg_match('/^([0-9]+)\.([0-9]+)\.([0-9]+)/',$ver, $matches);
		$version = array();
		$version['primary'] = (integer) $matches[1];
		$version['sub'] = (integer) $matches[2];
		$version['subsub'] = (integer) $matches[3];
	}
}

?>
