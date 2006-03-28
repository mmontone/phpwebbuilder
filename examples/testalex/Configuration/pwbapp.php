<?php
/**
 * Initializes the system for executing the application
 * 
 * */

require_once dirname(__FILE__) . '/ConfigReader.class.php';
$config_reader =& new ConfigReader();
$config_reader->read(dirname(__FILE__) . '/../config.ini');
require_once pwbdir . '/pwb.php';
session_start();

?>