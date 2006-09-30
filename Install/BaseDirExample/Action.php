<?php

if(isset($_REQUEST["app"])) define('app_class',$_REQUEST["app"]);
require_once dirname(__FILE__).'/Configuration/pwbapp.php';

ob_start('fatal_error_handler');

Application::launch();

ob_end_flush();

?>
