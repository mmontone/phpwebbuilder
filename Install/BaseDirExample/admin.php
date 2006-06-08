<?php

define('modules',
	"Model,Application,View,Instances,database, DefaultCMS");

define('app_class',"DefaultCMSApplication");

$_REQUEST["reset"]="yes";

require_once 'Action.php';

?>