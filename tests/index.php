<?php
if (!isset($_REQUEST['config'])) {
	$config = 'default.ini';
}
else {
	$config = $_REQUEST['config'];
}

$defines = parse_ini_file('config/' . $config . '.ini');

if (!$defines) {
	echo 'Bad request. Invalid configuration ' . $config . '<br />';
    echo 'Example request: http://localhost/pwb/tests?config=myconfig and place myconfig.ini in pwb/tests/config';

	exit;
}

foreach ($defines as $key => $value) {
	define($key, $value);
}

require_once '../pwb.php';
require_once 'pwbtests.php';
require_once 'PWBHtmlTestsReporter.class.php';


Application::instance();
$tests = & new PWBTests();
$tests->run(new PWBHtmlTestsReporter());

?>
