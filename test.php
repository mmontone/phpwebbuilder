<?
require_once "/var/www/cefi/Configuration/pwbapp.php";


$o1 =& new PersistentObject;
$o2 =& $o1;
$o3 = $o1;
var_dump($o1->__instance_id);
var_dump($o1->is($o2));
var_dump($o1->is($o3));

?>