<?
require_once "/var/www/cefi/Configuration/pwbapp.php";

$c =& new Collection;
$c->addAll(array(1,3,4,5,62,2));
print_r($c->toArray());
$d =& $c->map(lambda('$e', 'return $e*2;', get_defined_vars()));
$f =& $c->filter(lambda('$e', 'return $e%2==0;', get_defined_vars()));
print_r($d->toArray());
print_r($f->toArray());

?>
