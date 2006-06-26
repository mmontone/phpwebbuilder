<?php
ini_set('memory_limit', '32M');
if(isset($_REQUEST["app"])) define('app_class',$_REQUEST["app"]);
require_once 'install.php';
Application::launch();

?>
