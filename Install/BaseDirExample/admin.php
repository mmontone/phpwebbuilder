<?php

define('modules',
	"Model,Application,Instances,database,Controllers,DefaultCMS," .
	"flowviews,OldViews,templates,ajax,NewTemplates");

define('app_class',"DefaultCMSApplication");

$_REQUEST["reset"]="yes";

require_once 'Action.php';

?>