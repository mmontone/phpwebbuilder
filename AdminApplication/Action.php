<?php

if(isset($_REQUEST["app"])) define('app_class',$_REQUEST["app"]);

//require_once dirname(__FILE__).'/../Install/BaseDirExample/Configuration/pwbapp.php';
require_once (dirname(__FILE__) . '/../Install/BaseDirExample/Configuration/ConfigReader.class.php');
$config_reader =& ConfigReader::Instance();
$config_reader->load(dirname(__FILE__) . '/config.php');
require_once (pwbdir . 'pwb.php');


Application::launch();


?>