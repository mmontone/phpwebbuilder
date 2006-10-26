<?php

class DBDriver {
	var $session;

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


}

?>