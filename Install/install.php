<?php

define('modules', 'Core,Application,View');
define('app_class', 'InstallApplication');
define('pwbdir', dirname(__FILE__).'/../');
define('basedir', dirname(__FILE__));
define('app', 'InstallInstances');
define('page_renderer', 'AjaxPageRenderer');
$url='http://'.$_SERVER["HTTP_HOST"].dirname(dirname($_SERVER["PHP_SELF"]));
define('site_url', $url);
define('pwb_url', $url);
session_name(strtolower(app_class));
require_once dirname(__FILE__).'/../pwb.php';
?>
