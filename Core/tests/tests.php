<?php
require_once 'CollectionTests.class.php';

class CoreTests extends GroupTest {
	function CoreTests() {
		$this->GroupTest('Core tests');
		$this->addTestCase(new CollectionTests);
	}
}
?>
