<?php

class PgSQLDriver extends DBDriver {
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