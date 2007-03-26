<?php
require_once 'db_setup.php';

class MemoryTest extends UnitTestCase {
	var $db;

	function setUp() {
		global $setup_sql;
		$sqls = explode(';', $setup_sql);

		$this->db = & DBSession:: Instance();

		foreach ($sqls as $sql) {
			$this->db->query($sql);
		}
	}

	function tearDown() {
		$this->db->query('DROP TABLE `' . baseprefix . 'users`');
		$this->db->query('DROP TABLE `' . baseprefix . 'MenuSection`');
		$this->db->query('DROP TABLE `' . baseprefix . 'MenuItem`');
		$this->db->query('DROP TABLE `' . baseprefix . 'Role`');
		$this->db->query('DROP TABLE `' . baseprefix . 'RolePermission`');
		$this->db->query('DROP TABLE `' . baseprefix . 'UserRole`');
		$this->db->query('DROP TABLE `' . baseprefix . 'Session`');

	}
}
?>