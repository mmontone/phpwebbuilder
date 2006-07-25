<?php
require_once '/var/www/eurekagw/Configuration/pwbapp.php';
$f = & new CollectionField(array (
	'type' => 'Role',
	'joinTable' => 'UserRole'
));

$f->setID(1);
$col =& $f->collection;
$sql = $col->selectsql();
print_r($sql);echo '<br/>';
$db = & DB :: instance();
$res = $db->query($sql);
echo DB::lastError();echo '<br/>';
print_r($db->fetchRecord($res));
echo '<br/>';
$f->collection->map(lambda('&$r','echo getClass($r).$r->getId();',$a=array()));
echo '<br/>';
?>