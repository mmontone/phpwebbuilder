<?php
require_once 'install.php';
ini_set('memory_limit', '32M');
if ($_SESSION['action_dispatcher'] == null)
{
	$_SESSION['action_dispatcher'] =& new ActionDispatcher();
}
$app =& $_SESSION['action_dispatcher']->dispatch();
$app->render();
?>