<?php
require_once 'PersistenceTests.class.php';

class ModelTests extends GroupTest {
	function ModelTests() {
		$this->GroupTest('Model tests');
		$this->addTestCase(new PersistenceTests);
	}
}

?>
