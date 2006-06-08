<?php
ini_set('memory_limit', '32M');
require_once dirname(__FILE__).'/Configuration/pwbapp.php';
if ($_SESSION['action_dispatcher'] == null)
{
	$_SESSION['action_dispatcher'] =& new ActionDispatcher();
}
$app =& $_SESSION['action_dispatcher']->dispatch();
$app->render();
?>