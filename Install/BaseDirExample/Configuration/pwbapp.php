<?php
/**
 * Initializes the system for executing the application
 *
 * */

require_once (dirname(__FILE__) . '/ConfigReader.class.php');
$config_reader =& ConfigReader::Instance();
$config_reader->load(dirname(__FILE__) . '/../config.php');
require_once (pwbdir . 'pwb.php');

?>