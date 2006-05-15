<?

class AbstractDB {
	function fetchArray($res) {
		$arr = array();
		while ($rec = $this->fetchRecord($res)) $arr[]= $rec;
		return $arr;
	}
	function batchExec($sqls) {
		foreach($sqls as $sql) {
			if (trim($sql)!="") { //User Might have included a "" at the end
				$rec = $this->SQLExec($sql, FALSE, $this);
				$ret []= $this->fetchArray($rec);
			}
		}
		return $ret;
	}
	function queryDB($query){
		$res = $this->batchExec(array($query));
		return $res[0];
	}
}

class MySQLdb extends AbstractDB {
    function SQLExec ($sql, $getID, $obj, $rows=0) {
    	trace($sql. "<BR>");
        $this->openDatabase();
        $reg = mysql_query ($sql) or
        	die (print_backtrace(mysql_error() . ": $sql"));
        if ($getID) { $obj->setID(mysql_insert_id());};
        $rows = mysql_affected_rows();
        $this->closeDatabase();
        return $reg;
    }

    function query($sql) {
        $this->openDatabase();
        $reg = mysql_query ($sql) or
        	die (print_backtrace(mysql_error() . ": $sql"));
        $this->closeDatabase();
        return $reg;
    }

    function fetchrecord($res) {
    	return mysql_fetch_assoc($res);
    }
    function openDatabase() {
      mysql_connect(serverhost, baseuser, basepass) or
          die (print_backtrace(mysql_error()));
      mysql_select_db(basename) or
          die (print_backtrace(mysql_error()));
    }
    function closeDatabase() {
      mysql_close();
    }
    function escape($str) {
    	$this->openDatabase();
    	$ret = mysql_real_escape_string($str);
    	$this->closeDatabase();
    	return $ret;
    }
    function unescape($str) {
    	$ret = ereg_replace("\\\'","\'",$str);
    	return $ret;
    }
}

class PgSQLdb extends AbstractDB {
    var $conn;
    function SQLExec ($sql, $getID, $obj) {
        if ($getID) {
		$tempsql = "SELECT nextval('". $obj->tablename() . "_id_seq') as id";
		$thisclass = get_class($this);
		$db = new $thisclass;
		$resID = $db->SQLExec($tempsql, FALSE, $obj);
		$resID2 = $db->fetchrecord($resID);
		$obj->id=$resID2["id"];
		$sqlnew = ereg_replace("INSERT INTO ([[:alnum:]]*)\\(","INSERT INTO \\1 (id, ", $sql);
		$sql = str_replace("VALUES (","VALUES(". $obj->id.", ", $sqlnew);
	}
	$this->openDatabase();
	$reg = pg_query($sql);
	$this->closeDatabase();
        return $reg;
    }
    function fetchrecord($res) {
    	return pg_fetch_assoc($res);
    }
    function openDatabase() {
    $str = " host=".serverhost." user=".baseuser." password=".basepass." dbname=".basename;
      $this->conn = pg_pconnect($str);
    }
    function closeDatabase() {
      pg_close($this->conn);
    }
}


?>
