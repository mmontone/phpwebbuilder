<?

xdebug_start_profiling();

function showProfile(){
	xdebug_dump_function_profile(1);
}

register_shutdown_function('showProfile');

require_once 'pwb.php';



?>