<?php

	$a = array();
	$b = array();
	class A{}

	$aa =& new A;
	$a[1] =& $aa;
	echo count($a);
	unset($a[1]);
	echo count($a);
	?>