<?php
require_once 'install.php';
$_REQUEST["reset"]="yes";
$app_launcher =& new ApplicationLauncher();
$app_launcher->launch(app_class);

?>