<?php
require_once dirname(__FILE__).'/Configuration/pwbapp.php';
if ($_SESSION['action_dispatcher'] == null)
{
	$_SESSION['action_dispatcher'] =& new ActionDispatcher();
}

$_SESSION['action_dispatcher']->dispatch();
header('location:Action.php');

?>