<?php
require_once('SelectTest.class.php');

class WidgetsTests extends GroupTest {
	function WidgetsTests() {
		$this->GroupTest('Widget tests');
		$this->addTestCase(new SelectTest);
	}
}

?>
