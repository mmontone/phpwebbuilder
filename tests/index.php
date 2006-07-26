<?php
require_once 'testsconfig.php';
require_once '../pwb.php';
require_once 'pwbtests.php';
require_once 'PWBHtmlTestsReporter.class.php';

Application::instance();
$tests = & new PWBTests();
$tests->run(new PWBHtmlTestsReporter());

?>
