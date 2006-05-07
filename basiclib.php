<?php
/**
 * Some basic functions.
 */

function includefile(&$file) {
	if (is_dir($file)) {
			$gestor=opendir($file);
			while (false !== ($f = readdir($gestor))) {
				if (substr($f,-1)!='.')
					includefile(implode(array($file,'/',$f)));
			}
	} else {
		if (substr($file, -4)=='.php') {
                  //echo "Including file: " . $file;
                  require_once($file);
		}
	}
}
function includemodule (&$module){
	$moddir = implode(array(pwbdir,'/',$module));
	$modf = implode(array($moddir,'/',$module,'.php'));
	if (file_exists($modf)){
		require_once($modf);
	} else{
		includefile($moddir);
	}
}

/**
 * Fills a line in the log
 */
function trace($str) {
	trigger_error($str, E_USER_NOTICE);
}

/**
 * Logs all parameters sent by request.
 */
function trace_params(){
	foreach($_REQUEST as $name=>$value)
			trace($name ."=". $value."<BR>");
}

/**
 * Finds all the subclases for the specified class (works only for PWB objects!)
 */
function get_subclasses($str){
	$arr = get_declared_classes();
	$ret = array();
	foreach ($arr as $o) {
		$vars = get_class_vars($o);
		if (isset($vars["isClassOfPWB"]) &&
			$vars["isClassOfPWB"]){
 			$obj = new $o;
			if (is_subclass_of($obj, $str))
				$ret[]=$o;
		}
	}
	return $ret;
}

/**
 * This function checks if the user with $id id, has the permission $permission
 */
function fHasPermission($id, $permission){
	trace("Permission Needed is: ".$permission);
	$role = new Role;
	return $role->userHasPermission($id, $permission);
}

/**
 * This function checks if the user with $id id, has the permission
 * for the action $act on the object $obj.
 */
function fHasAnyPermission($id, $obj, $act){
	return fHasPermission($id, array("*",
				"*=>".$act,
				$obj."=>*",
				$obj."=>".$act));
}

/**
 * prints the backtrace.
 */
function backtrace(){
	print_r(debug_backtrace());
}

function print_backtrace($error) {
  echo backtrace_string($error);
}

function backtrace_string($error) {
    $back_trace = debug_backtrace();
    $ret = "<h1>$error</h1>";
    foreach ($back_trace as $trace) {
        $ret .= "<br/><b> {$trace['file']}: {$trace['line']} ({$trace['function']})</b>";
        		//$ret .= print_r($trace['args'], TRUE);
    }
    return $ret;
}

/***************/
/* Assertions  */
/***************/

// Active assert and make it quiet
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_QUIET_EVAL, 1);

// Create a handler function
function my_assert_handler($file, $line, $code)
{
   echo "<hr>Assertion Failed:
       <b>File</b> '$file'<br />
       <b>Line</b> '$line'<br />
       <b>Code</b> '$code'<br />";
   print_backtrace('');
   echo  "</hr>";
}

// Set up the callback
assert_options(ASSERT_CALLBACK, 'my_assert_handler');


/* Useful functions */
function &array_current(&$array) {
	if (!current($array)) return null; // Out of limit
	return $array[key($array)];
}

function &array_next(&$array) {
	$current =& array_current($array);
	next($array);
	return $current;
}
?>
