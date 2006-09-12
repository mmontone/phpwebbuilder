<?php
require_once 'MultipleDispatchTests.class.php';

class LibTests extends GroupTest {
	function LibTests() {
		$this->GroupTest('Lib tests');
		$this->addTestCase(new MultipleDispatchTests);
	}
}
?>
