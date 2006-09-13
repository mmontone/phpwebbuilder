<?php
require_once 'EventsTests.class.php';
require_once 'CollectionTests.class.php';
require_once 'SystemTests.class.php';


class CoreTests extends GroupTest {
	function CoreTests() {
		$this->GroupTest('Core tests');
		$this->addTestCase(new SystemTests);
		//$this->addTestCase(new EventsTests);
		//$this->addTestCase(new CollectionTests);

	}
}
?>
