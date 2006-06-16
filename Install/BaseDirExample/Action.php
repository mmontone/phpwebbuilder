<?php
/**
* Launches the application defined in config.ini
*/
require_once dirname(__FILE__).'/Configuration/pwbapp.php';
session_name(strtolower(app_class));
$app_launcher =& new ApplicationLauncher();
$app_launcher->launch(app_class);

?>
