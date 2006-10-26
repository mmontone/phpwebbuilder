<?php

class DBTest extends UnitTestCase {

    function DBTest() {
    	$this->UnitTestCase('Database tests');
    }

    function testOpenDatabase() {
		$this->assertTrue($this->db->openDatabase(), 'Openning database');
	}

	function testQuery() {
		$this->db->SQLExec('CREATE TABLE `numbers` (
							`Num` INT) TYPE = innodb;');
		$this->db->SQLExec('INSERT INTO numbers (Num) VALUES (5)');
		$res =& $this->db->SQLExec('SELECT * FROM numbers');
		$res = $this->db->fetchArray($res);
		$this->assertEqual($res[0]['Num'], '5', 'Running a simple query');
		$this->db->SQLExec('DROP TABLE `numbers`');
	}
}

class MySQLDBTest extends DBTest {
	function MySQLDBTest() {
		$this->UnitTestCase('MySQL driver tests');
	}

	function setUp() {
		$this->db =& new MySQLdb;
	}
}

class TestsDBTest extends DBTest {
	function TestsDBTest() {
		$this->UnitTestCase('Checking tests database connection');
	}

	function setUp() {
		$this->db =& new TestsDb;
	}

	function testSettingUp() {
		TestsDb::setUp();
		$db =& DBSession::Instance();
		$this->assertEqual(getClass($db), 'testsdb', 'Checking setup');
	}

/*
	function testRelease() {
		// Need to plug a new site configuration but now i can't do that
		// because of the "defines"
		TestsDb::setUp();
		DBSession::release();
		$conf =& SiteConfig::getInstance();
		$cong->setDBDriver('MySQLdb');
		$db =& DBSession::Instance();
		$this->assertEqual(getClass($db), 'MySQLdb');
	}
*/
}

?>