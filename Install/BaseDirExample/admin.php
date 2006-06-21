<?php

define('modules',
	"Core,Application,Model,Instances,database,View,DefaultCMS");

define('app_class',"DefaultCMSApplication");

$_REQUEST["reset"]="yes";

require_once 'Action.php';

?>