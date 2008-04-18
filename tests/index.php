<?php
if (!isset($_REQUEST['config'])) {
	$config = 'default';
}
else {
	$config = $_REQUEST['config'];
}

require_once dirname(__FILE__).'/../Install/BaseDirExample/Configuration/ConfigReader.class.php';
$config_reader =& ConfigReader::Instance();
$config_reader->load(dirname(__FILE__) . '/config/default.ini');



//require_once '../pwb.php';
require_once 'pwbtests.php';
require_once 'PWBHtmlTestsReporter.class.php';


Application::instance();
$tests = & new PWBTests();
$tests->run(new PWBHtmlTestsReporter());

?>
