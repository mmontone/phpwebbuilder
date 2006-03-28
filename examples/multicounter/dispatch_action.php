<?php
require_once 'pwbapp.php';

session_start();

if ($_SESSION['action_dispatcher'] == null)
{
	$_SESSION['action_dispatcher'] =& new ActionDispatcher();
}

$_SESSION['action_dispatcher']->dispatch();


?>