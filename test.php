<?php
class VH
{
	var $x;

}

$e =& new E;
$e->x = 2;
//$a = array();
//$a['e'] =& $e;
//$b = $a;
//$a = array('e'=>$e);
$vh =& new E;
$vh->x =& $e;
$a = array('e'=>$vh);
$e->x = 3;
echo $e->x;
echo $a['e']->x->x;


?>