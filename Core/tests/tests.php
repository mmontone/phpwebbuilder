<?php
require_once 'EventsTests.class.php';
require_once 'CollectionTests.class.php';
require_once 'SystemTests.class.php';
require_once 'ConditionsTests.class.php';


class CoreTests extends GroupTest {
	function CoreTests() {
		$this->GroupTest('Core tests');
		$this->addTestCase(new SystemTests);
		$this->addTestCase(new CollectionTests);
		$this->addTestCase(new ConditionsTests);
		//$this->addTestCase(new EventsTests);
	}
}
?>
