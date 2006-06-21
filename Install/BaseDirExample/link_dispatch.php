<?php
ini_set('memory_limit', '32M');
session_name(strtolower($_REQUEST["app"]));
require_once dirname(__FILE__).'/Configuration/pwbapp.php';
$app =& Application::getInstanceOf($_REQUEST['app']);
$app->historylistener->receivedToken($_REQUEST['token']);
$app->standardRender();
?>
