<?php

class DB {
	var $lastError = 'No Error';
	var $lastSQL = '';

	function DB() {
		$this->initialize();
	}

	function initialize() {}

	function fetchArray($res) {
		$arr = array();
		if ($res===true) {
			$arr[]="Query suceeded";
		} else if ($res===false) {
			$arr[]="Query failed " . mysql_error();
			trigger_error(mysql_error(),E_USER_NOTICE);
		} else {
			while ($rec = $this->fetchRecord($res)) $arr[]= $rec;
		}
		return $arr;
	}
	function batchExec($sqls) {
		foreach($sqls as $sql) {
			if (trim($sql)!="") { //User Might have included a "" at the end
				$rec = $this->query($sql);
				$ret []= array($sql, $this->fetchArray($rec));
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
		return $res[0][1];
	}
	function &Instance(){
		if (!isset($_SESSION[sitename]['DB'])){
			$c = constant('DBObject');
			$_SESSION[sitename]['DB'] =& new $c;
		}
		return $_SESSION[sitename]['DB'];
	}
}
?>