<?php
require_once '/var/www/cefi/Configuration/pwbapp.php';
$db =& DB::instance();
print_r($db);
echo serverhost." ". baseuser ." ". basepass;
$db->openDatabase();
print_r($db);

?>