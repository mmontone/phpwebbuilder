<?php
/*
* Created on 01-mar-2006
*
* To change the template for this generated file go to
* Window - Preferences - PHPeclipse - PHP - Code Templates
*/

require_once 'pwbapp.php';

session_start();

$app_launcher =& new ApplicationLauncher();
$app_launcher->launch(app_class);

?>
