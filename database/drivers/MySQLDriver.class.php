<?

class MySQLdb extends DB {
	var $conn;
	var $tables_type = 'MyISAM';

    function SQLExec ($sql, $getID=false, $obj=null, $rows=0) {
       	//echo($sql. '<br/>');
		$this->lastSQL = $sql;
        $reg = $this->query ($sql);
        if ($getID) { $obj->setID(mysql_insert_id());};
        $rows = mysql_affected_rows();
        return $reg;
    }

    function initialize() {
		if (defined('tables_type')) {
			$this->setTablesType(tables_type);
		}
    }

    function beginTransaction() {
    	$this->openDatabase();
        /* If transactions are not supported, go on silently (logging is another option)*/
        mysql_query ("START TRANSACTION;"); /* or
            die (print_backtrace(mysql_error() . ": $sql"));*/
        trigger_error("Starting transaction");
        $this->closeDatabase();
    }

    function commit() {
        $this->openDatabase();
        /* If transactions are not supported, go on silently (logging is another option)*/
        mysql_query ("COMMIT;"); /*or
            die (print_backtrace(mysql_error() . ": $sql"));*/
        trigger_error("Comitting transaction");
        $this->closeDatabase();
    }

    function rollBack() {
        $this->openDatabase();
        /* If transactions are not supported, go on silently (logging is another option)*/
        mysql_query ("ROLLBACK;"); /*or
            die (print_backtrace(mysql_error() . ": $sql"));*/
        trigger_error("Rolling back transaction");
        $this->closeDatabase();
    }

    function query($sql) {
    	trace($sql. '<br/>');
		$this->openDatabase();
		$this->lastSQL = $sql;
        $reg = mysql_query ($sql);
        $this->closeDatabase();
        if (!$reg){
        	$this->lastError=mysql_error() . ': '.$sql;
        	return false;
        }
        return $reg;
    }

    function fetchrecord($res) {
    	return mysql_fetch_assoc($res);
    }
    function openDatabase() {
    	if (!$this->conn){
	      $this->conn = mysql_connect(serverhost, baseuser, basepass);
	      if (!$this->conn){
	          $this->lastError = mysql_error();
	          return false;
	      }
	      $b = mysql_select_db(basename);
	      if (!$b){
	          $this->lastError = mysql_error();
	          return false;
	      }
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
		$this->openDatabase();
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
