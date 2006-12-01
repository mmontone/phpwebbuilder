<?php
/**
 * Initializes the system for executing the application
 *
 * */

compile_once (dirname(__FILE__) . '/ConfigReader.class.php');
$config_reader =& new ConfigReader();
$config_reader->load(dirname(__FILE__) . '/../config.php');
compile_once (pwbdir . '/pwb.php');

?>