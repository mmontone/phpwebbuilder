<?
$t = time();
xdebug_start_profiling();


function showProfile(){
	global $t;
	echo "duration: ".time() - $t ;
	xdebug_dump_function_profile(4);
	echo "duration: ".time() - $t ;

}

register_shutdown_function('showProfile');

require_once 'pwb.php';



?>