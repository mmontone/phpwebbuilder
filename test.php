<?php

class Test {
	function Test(){
		echo "creating me";
	}
	function lala (){
		echo "lala";
	}
	function __get($prop, &$return){
		$return = "called $prop";
		return true;
	}
}

class Test2 extends Test {
	var $lala = "pirulo";

}

overload(Test2);
$t =& new Test2;
echo $t->lala;
echo $t->lele;

?>