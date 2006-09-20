<?php
require_once 'SelectTest.class.php';
require_once 'OptionalSelectTest.class.php';

class WidgetsTests extends GroupTest {
	function WidgetsTests() {
		$this->GroupTest('Widget tests');
		$this->addTestCase(new SelectTest);
		$this->addTestCase(new OptionalSelectTest);
	}
}

?>
