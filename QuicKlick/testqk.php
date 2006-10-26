<?php
/*
 * Created on 01/09/2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
define('app_class','DefaultCMSApplication');
define('pwbdir', dirname(dirname(__FILE__)));
define('page_renderer', 'StandardPageRenderer');
define('db_driver', 'MySQLdb');
define('basedir', pwbdir.'/Install/BaseDirExample');
define('pwb_url','http://'.$_SERVER['SERVER_NAME'].dirname(dirname($_SERVER['SCRIPT_NAME'])));
require_once pwbdir.'/pwb.php';
require_once 'QuicKlick.php';

function &create_app(){
	Application::restart();
	return Application::getInstanceOf('DefaultCMSApplication');
}

function &create_logged_app(){
	$app =& create_app();
	$app->component->body->comp_username->setValue($u='admin');
	$app->component->body->comp_password->setValue($p='PWB-Admin');
	$app->component->body->login_do();
	return $app;
}

$qc =& new QuicKlick('Start','Application::restart();
	return Application::getInstanceOf("DefaultCMSApplication");', 1, 5);
//$qc =& new QuicKlick('Logged','create_logged_app',2, 10);

?>