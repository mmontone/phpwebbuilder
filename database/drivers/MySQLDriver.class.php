<?

class MySQLDriver extends DBDriver {
	var $tables_type = 'MyISAM';
	function getLastId(){
		return mysql_insert_id();
	}
	function getRowsAffected(&$result){
		return mysql_affected_rows();
	}
    function initialize() {
		if (defined('tables_type')) {
			$this->setTablesType(tables_type);
		}
    }

	function getError(){
		return mysql_error();
	}

	function basicQuery(&$conn,$sql){
		return mysql_query ($sql);
	}
	function basicBeginTransaction(){
		mysql_query ("START TRANSACTION;");
	}
	function basicCommit(){
		mysql_query ("COMMIT;");
	}
	function basicRollback(){
			mysql_query ("ROLLBACK;");
	}
    function fetchrecord($res) {
    	return @mysql_fetch_assoc($res);
    }
    function basicPConnect(){
    	return mysql_pconnect(constant('serverhost'), constant('baseuser'), constant('basepass'));
    }
    function basicConnect(){
    	return mysql_connect(constant('serverhost'), constant('baseuser'), constant('basepass'));
    }
    function selectDB(){
    	return mysql_select_db(constant('basename'));
    }
    function closeDatabase() {
      @mysql_close($this->conn);
      @mysql_close($this->pconn);
      unset($this->conn);
      unset($this->pconn);
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
		preg_match('/^([0-9]+)\.([0-9]+)\.([0-9]+)/',$ver, $matches);
		$version = array();
		$version['primary'] = (integer) $matches[1];
		$version['sub'] = (integer) $matches[2];
		$version['subsub'] = (integer) $matches[3];
	}
	function getTablesSQL(){
		return "SHOW TABLES FROM `" . basename . '` LIKE \''.baseprefix.'%\'';
    }
	function getTableSQL($table){
		return "SHOW TABLES FROM `" . basename . "` LIKE '" . $table ."'";
    }
    function idFieldType(){
    	return "int(11) unsigned NOT NULL AUTO_INCREMENT";
    }
    function referenceType(){
    	return "int(11) unsigned";
    }
}

?>
