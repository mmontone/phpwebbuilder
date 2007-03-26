<?php

/*
class TestsDB extends MySQLdb {
	function setUp() {
		$_SESSION['DB'] = & new TestsDB;
	}

	function release() {
		unset($_SESSION['DB']);
	}

	function LoginDB() {
		$this->query('CREATE DATABASE IF NOT EXISTS `pwb_tests`');
	}

	function openDatabase() {
		if (!$this->conn) {
			$this->conn = mysql_connect(serverhost, baseuser, basepass);
			if (!$this->conn) {
				$this->lastError = mysql_error();
				return false;
			}
			$b = mysql_select_db('pwb_tests');
			if (!$b) {
				$this->lastError = mysql_error();
				return false;
			}
		}
		return true;
	}
}
*/
?>