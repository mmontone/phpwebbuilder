<?php

define('modules',
	"BaseClasses,Application,Model,View,Instances,database,DefaultCMS,View");

define('app_class',"DefaultCMSApplication");

$_REQUEST["reset"]="yes";

require_once 'Action.php';

?>