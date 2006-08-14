<?php

define('modules', 'Core,Application,View,database,DefaultCMS/Administrator');
define('app_class', 'InstallApplication');
define('pwbdir', dirname(__FILE__).'/../');
define('basedir', dirname(__FILE__));
define('app', 'InstallInstances');
define('page_renderer', 'StandardPageRenderer');
define('debugview', 'false');
define('templates', 'enabled');
$url=dirname($_SERVER["PHP_SELF"]);
define('site_url', $url);
define('pwb_url', $url.'/../');
session_name(strtolower(app_class));
require_once dirname(__FILE__).'/../pwb.php';
?>
