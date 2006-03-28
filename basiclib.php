<?php
/**
 * Some basic functions.
 */

function includefile($file) {
	if (is_dir($file)) {
			$gestor=opendir($file);
			while (false !== ($f = readdir($gestor))) {
				if (!ereg($f,"\.$"))
					includefile($file."/".$f);
			}
	} else {
		if (ereg(".*php$",$file)){
                 //echo ( "Including file: " . $file . " </br>");
                  require_once($file);
		}
	}
}

function includemodule ($module){
	$moddir = pwbdir."/".$module;
	if (file_exists($moddir."/".$module.".php")){
		trace("including ".$moddir);
		require_once($moddir."/".$module.".php");
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
        $ret .= "<b> {$trace['file']}: {$trace['line']} ({$trace['function']})</b>" .
        		print_r($trace['args'], TRUE)."</br>";
    }
    return $ret;
}
?>
