<?php
require_once 'db_setup.php';

class MemoryTest extends UnitTestCase {
	var $db;

	function setUp() {
		global $setup_sql;
		$sqls = explode(';', $setup_sql);

		$this->db = & DB :: Instance();

		foreach ($sqls as $sql) {
			$this->db->SQLExec($sql);
		}
	}

	function tearDown() {
		$this->db->SQLExec('DROP TABLE `' . baseprefix . 'users`');
		$this->db->SQLExec('DROP TABLE `' . baseprefix . 'MenuSection`');
		$this->db->SQLExec('DROP TABLE `' . baseprefix . 'MenuItem`');
		$this->db->SQLExec('DROP TABLE `' . baseprefix . 'Role`');
		$this->db->SQLExec('DROP TABLE `' . baseprefix . 'RolePermission`');
		$this->db->SQLExec('DROP TABLE `' . baseprefix . 'UserRole`');
		$this->db->SQLExec('DROP TABLE `' . baseprefix . 'Session`');

	}
}
?>