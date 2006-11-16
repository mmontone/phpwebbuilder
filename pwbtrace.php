<?

xdebug_start_trace();

function showTrace(){
	xdebug_dump_function_trace();
}

register_shutdown_function('showTrace');

require_once 'pwb.php';

?>