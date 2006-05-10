<?php
class Test {
var $y = "asdf";
var $z;
(string) function lala ($x){
	echo $x;
}

}
$t = new Test;
echo "lala vale ".$t->lala('hola');

echo "<br/>x es ".gettype($t->x)." y vale $t->x";
echo "<br/>y es ".gettype($t->y)." y vale $t->y";
echo "<br/>z es ".gettype($t->z)." y vale $t->z";

?>