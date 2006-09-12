<?php
require_once simpletest_dir . '/simpletest.php';
require_once simpletest_dir . '/unit_tester.php';
require_once '../lib/tests/tests.php';
require_once '../Core/tests/tests.php';
require_once '../database/tests/tests.php';
require_once '../Model/Tests/tests.php';
require_once '../DefaultCMS/tests/tests.php';
require_once '../Application/Tests/tests.php';
require_once '../Application/components/Widgets/tests/tests.php';


class PWBTests extends GroupTest {
	function PWBTests() {
	    $this->GroupTest('PWB Tests');
		$tests = explode(',', tests);
		foreach($tests as $test) {
			$class = $test . 'Tests';
			$this->addTestCase(new $class);
		}
		//$this->addTestCase(new CoreTests);
		//$this->addTestCase(new WidgetsTests);
		//$this->addTestCase(new DatabaseTests);
		//$this->addTestCase(new ModelTests);
		//$this->addTestCase(new UsersAndPermissionsTests);
		//$this->addTestCase(new MemoryTests);
    }
}

?>
