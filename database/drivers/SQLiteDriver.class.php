<?

class SQLiteDriver extends DBDriver {
	function getLastId(){
		return sqlite_last_insert_rowid($this->pconn);
	}
	function getRowsAffected(&$result){
		return sqlite_num_rows($result);
	}

	function getError(){
		return sqlite_error_string(sqlite_last_error($this->pconn));
	}

	function basicQuery(&$conn,$sql){
		return sqlite_query ($conn,$sql,SQLITE_ASSOC);
	}
	function basicBeginTransaction(&$conn){
		sqlite_query ($conn,"START TRANSACTION;",SQLITE_ASSOC);
	}
	function basicCommit(&$conn){
		sqlite_query ($conn,"COMMIT;",SQLITE_ASSOC);
	}
	function basicRollback(&$conn){
		sqlite_query ($conn,"ROLLBACK;",SQLITE_ASSOC);
	}
    function fetchrecord($res) {
    	return @sqlite_fetch_array($res,SQLITE_ASSOC);
    }
    function basicPConnect(){
    	return sqlite_popen(constant('basename'));
    }
    function basicConnect(){
    	return sqlite_open(constant('basename'));
    }
    function selectDB(){    }
    function closeDatabase() {
      @sqlite_close($this->conn);
      @sqlite_close($this->pconn);
      unset($this->conn);
      unset($this->pconn);
    }
    function escape($str) {
    	$ret = sqlite_escape_string($str);
    	return $ret;
    }
    function unescape($str) {
    	$ret = ereg_replace("''","'",$str);
    	return $ret;
    }

    // SQL

	function showColumnsFromTableSQL($table) {
		return "SHOW COLUMNS FROM `" . $table."`";
	}

	function dropColumnSQL($column) {
		 return "DROP COLUMN `$column`";
	}
}

?>
