<?php
compile_once ('SelectTest.class.php');
compile_once ('OptionalSelectTest.class.php');

class WidgetsTests extends GroupTest {
	function WidgetsTests() {
		$this->GroupTest('Widget tests');
		$this->addTestCase(new SelectTest);
		$this->addTestCase(new OptionalSelectTest);
	}
}

?>
