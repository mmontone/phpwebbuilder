<?php

define('modules', 'Core,Application,View,database,DefaultCMS/Administrator,Model,BugNotifier,QuicKlick');
define('app_class', 'InstallApplication');
define('pwbdir', dirname(__FILE__).'/../');
define('basedir', dirname(__FILE__));
define('app', 'InstallInstances');
define('page_renderer', 'StandardPageRenderer');
define('debugview', 'false');
define('templates', 'enabled');
define('error_reporting', E_ERROR | E_WARNING);
$url=dirname($_SERVER["PHP_SELF"]);
define('site_url', $url);
define('pwb_url', $url.'/../');
session_name(strtolower(app_class));
require_once dirname(__FILE__).'/../pwb.php';
?>
