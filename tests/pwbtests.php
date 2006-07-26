<?php
require_once '../simpletest/simpletest.php';
require_once '../simpletest/unit_tester.php';
require_once '../database/tests/tests.php';
require_once '../Model/Tests/tests.php';
require_once '../DefaultCMS/tests/tests.php';
require_once '../Application/Tests/tests.php';

class PWBTests extends GroupTest {
	function PWBTests() {
	    $this->GroupTest('PWB Tests');
		$this->addTestCase(new DatabaseTests);
		//$this->addTestCase(new ModelTests);
		$this->addTestCase(new UsersAndPermissionsTests);
		$this->addTestCase(new MemoryTests);
    }
}

?>
