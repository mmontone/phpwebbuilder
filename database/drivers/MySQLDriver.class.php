<?

class DB {
	var $lastError = 'No Error';
	var $lastSQL = '';
	function fetchArray($res) {
		$arr = array();
		if ($res===true) {
			$arr[]="Query suceeded";
		} else if ($res===false) {
			$arr[]="Query failed";
		} else {
			while ($rec = $this->fetchRecord($res)) $arr[]= $rec;
		}
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
	function lastError(){
		$db =& DB::instance();
		return $db->lastError;
	}
	function lastSQL(){
		$db =& DB::instance();
		return $db->lastSQL;
	}
	function queryDB($query){
		$res = $this->batchExec(array($query));
		return $res[0];
	}
	function &Instance(){
		if ($_SESSION[sitename]['DB']==null){
			$c = constant('DBObject');
			$_SESSION[sitename]['DB'] =& new $c;
		}
		return $_SESSION[sitename]['DB'];
	}
}

class MySQLdb extends DB {
	var $conn;
    function SQLExec ($sql, $getID, $obj, $rows=0) {
    	trace($sql. '<br/>');
		$this->openDatabase();
		$this->lastSQL = $sql;
        $reg = $this->query ($sql);
        if ($getID) { $obj->setID(mysql_insert_id());};
        $rows = mysql_affected_rows();
        return $reg;
    }

    function query($sql) {
    	trace($sql. '<br/>');
		$this->openDatabase();
		$this->lastSQL = $sql;
        $reg = mysql_query ($sql);
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
      mysql_close($this->conn);
    }
    function escape($str) {
    	$ret = mysql_real_escape_string($str);
    	return $ret;
    }
    function unescape($str) {
    	$ret = ereg_replace("\\\'","\'",$str);
    	return $ret;
    }
}

class PgSQLdb extends DB {
    var $conn;
    function SQLExec ($sql, $getID, $obj) {
        if ($getID) {
		$tempsql = "SELECT nextval('". $obj->tablename() . "_id_seq') as id";
		$thisclass = getClass($this);
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
