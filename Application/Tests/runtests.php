<?php

require_once('/var/www/pwb/simpletest/unit_tester.php');
require_once('/var/www/pwb/simpletest/reporter.php');
require_once('tests.php');
$tc=& new TestComponents;
$tc->run(new HtmlReporter());
?>
