<?php
/*
 * Created on 26/05/2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once dirname(__FILE__).'/../Configuration/pwbapp.php';
require_once dirname(__FILE__) . '/uitests/uitests.php';

$app_launcher =& new ApplicationLauncher();
$app_launcher->launch('UITestsApplication');
?>
