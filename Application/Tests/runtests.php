<?php

require_once('/var/www/pwb/simpletest/unit_tester.php');
require_once('/var/www/pwb/simpletest/reporter.php');
require_once('TestMemoryComponents.class.php');
require_once('TestMemoryLogins.class.php');
require_once('TestMemoryComponentsListeners.class.php');
require_once('TestMemoryLoginsListeners.class.php');
require_once('TestMemoryComponentsView.class.php');
require_once('TestMemoryLoginsView.class.php');
require_once('TestMemoryLoginsListenersView.class.php');
require_once('TestMemoryComponentsListenersView.class.php');
require_once('TestMemoryChangeBody.class.php');
$test = &new GroupTest('All file tests');
	/*$test->addTestCase(new TestMemoryComponents);
	$test->addTestCase(new TestMemoryLogins);
	$test->addTestCase(new TestMemoryComponentsListeners);
	$test->addTestCase(new TestMemoryLoginsListeners);
	$test->addTestCase(new TestMemoryLoginsView);*/
	//$test->addTestCase(new TestMemoryLoginsListenersView);
	$test->addTestCase(new TestMemoryChangeBody);
	/*$test->addTestCase(new TestMemoryComponentsView);
	$test->addTestCase(new TestMemoryComponentsListenersView);*/
$test->run(new HtmlReporter());

?>