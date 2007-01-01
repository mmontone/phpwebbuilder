<?php

define('modules', 'Core,Application,View,database,Model,BugNotifier,DefaultCMS,QuicKlick,Instances');
define('app_class', 'InstallApplication');
define('pwbdir', dirname(dirname(__FILE__)).'/');
define('basedir', '');
define('app', 'InstallInstances');

define('page_renderer', 'StandardPageRenderer');
define('debugview', 'false');
define('templates', 'enabled');
define('error_reporting', E_ERROR | E_WARNING | E_PARSE |E_COMPILE_ERROR);
//define('error_reporting', E_ALL);
define('site_url', '');
define('pwb_url', '../');
define('compile', '');
session_name(strtolower(app_class));
require_once dirname(__FILE__).'/BaseDirExample/Configuration/ConfigReader.class.php';
require_once dirname(__FILE__).'/../pwb.php';
?>
