<?php

	class A {
    function A () {
        $this->b =& new B($this);
    }
}

class B {
    function B ($parent = NULL) {
        $this->parent =& $parent;
    }
}

/*
for ($i = 0 ; $i < 1000000 ; $i++) {
    $a =& new A();
}*/

$x =& new A;
$y =& $x;
$z =& $x;
$n = null;
$z =null;
var_dump($y);
var_dump($z);
	?>