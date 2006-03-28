<?php

function &set_single_instance_of($class){
	if (!isset($_ENV["singletons"][$class])){
		$_ENV["singletons"][$class] =& new $class; 
	}
	return $_ENV["singletons"][$class];
}

?>
