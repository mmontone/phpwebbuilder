<?php
error_reporting(E_ERROR | E_WARNING);
define('app_class',$_REQUEST['app_class']);
session_id($_REQUEST['sid']);
require_once $_REQUEST['basedir'].'/Configuration/pwbapp.php';
require_once 'QuicKlick.php';
$qk =&Session::getAttribute('QuicKlick');
$qk->check();

?>
