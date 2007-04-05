<?php
//require_once 'MultipleDispatchTests.class.php';
compile_once('MultipleDispatch2Tests.class.php');
require_once 'QSortTests.class.php';

class LibTests extends GroupTest {
	function LibTests() {
		$this->GroupTest('Lib tests');
		$this->addTestCase(new MultipleDispatchTests);
	//	$this->addTestCase(new MultipleDispatch2Tests);
		$this->addTestCase(new QSortTests);
	}
}
?>
