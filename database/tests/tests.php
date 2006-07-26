<?php

require_once 'TestsDB.class.php';
require_once 'DBTest.class.php';

class DatabaseTests extends GroupTest {
	function DatabaseTests() {
		$this->GroupTest('Database tests');
		$this->addTestCase(new MySQLDBTest);
		$this->addTestCase(new TestsDBTest);
	}
}
?>
