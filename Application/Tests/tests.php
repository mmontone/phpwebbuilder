<?php

require_once('MemoryTest.class.php');
require_once('TestMemoryComponents.class.php');
require_once('TestMemoryLogins.class.php');
require_once('TestMemoryComponentsListeners.class.php');
require_once('TestMemoryLoginsListeners.class.php');
require_once('TestMemoryComponentsView.class.php');
require_once('TestMemoryLoginsView.class.php');
require_once('TestMemoryLoginsListenersView.class.php');
require_once('TestMemoryComponentsListenersView.class.php');
require_once('TestMemoryChangeBody.class.php');

class MemoryTests extends GroupTest {
	function MemoryTests() {
		$this->GroupTest('Memory tests');
		//$this->addTestCase(new TestMemoryComponents);
		//$this->addTestCase(new TestMemoryLogins);
		//$this->addTestCase(new TestMemoryComponentsListeners);
		$this->addTestCase(new TestMemoryLoginsListeners);
		//$this->addTestCase(new TestMemoryLoginsView);
		//$this->addTestCase(new TestMemoryLoginsListenersView);
		$this->addTestCase(new TestMemoryChangeBody);
		//$this->addTestCase(new TestMemoryComponentsView);
		//$this->addTestCase(new TestMemoryComponentsListenersView);
	}
}

?>