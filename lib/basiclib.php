<?php
/**
 * Some basic functions.
 */

function getfilesrec ($pred, $dir){
	if (is_dir($dir)) {
			$ret = array();
			$gestor=opendir($dir);
			while (false !== ($f = readdir($gestor))) {
				if (substr($f,-1)!='.')
					$ret = array_merge(getfilesrec($pred, implode(array($dir,'/',$f))), $ret);
			}
		return $ret;
	} else {
		if ($pred($dir)){
			return array($dir);
		} else {
			return array();
		}
	}
}

function includefile(&$file) {
	foreach(getfilesrec($lam = lambda('$file','return $v=substr($file, -4)==".php";', $a=array()), $file) as $f){
        require_once($f);
	}
	delete_lambda($lam);
}

function includemodule ($module){
	$modf = implode(array($module,'/',basename($module),'.php'));
	if (file_exists($modf)){
		require_once($modf);
	} else{
		trigger_error('Falling back to includefile for '.$module,E_USER_NOTICE);
		includefile($module);
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

$PWBclasses = array();

function find_subclasses(){
	global $PWBclasses;
	$arr = get_declared_classes();
	$ret = array();
	foreach ($arr as $o) {
		$vars = get_class_vars($o);
		if (isset($vars["isClassOfPWB"]) &&
			$vars["isClassOfPWB"]){
			$PWBclasses[strtolower($o)] = array();
			$pcs = get_superclasses($o);
			foreach($pcs as $pc){
				$PWBclasses[$pc][]=$o;
			}
		}
	}
}

function get_subclasses($str){
	global $PWBclasses;
	if (count($PWBclasses)==0) find_subclasses();
	return $PWBclasses[strtolower($str)];
}

function get_superclasses($str){
	global $PWBclasses;
	$ret = array();
	$pc = get_parent_class($str);
	while($pc != ''){
		$ret[]=strtolower($pc);
		$pc = get_parent_class($pc);
	}
	return $ret;
}

function get_related_classes($str){
	return array_merge(get_superclasses($str), get_subclasses($str));
}
/**
 * This function checks if the user with $id id, has the permission $permission
 */
function fHasPermission($id, $permission){
	$u =& User::logged();
	return $u->hasPermission($permission);
}

/**
 * This function checks if the user with $id id, has the permission
 * for the action $act on the object $obj.
 */
function fHasAnyPermission($id, $obj, $act){
	return fHasPermission($id, '*') ||
			fHasPermission($id, 	'*=>'.$act) ||
			fHasPermission($id, $obj.'=>*') ||
			fHasPermission($id, $obj.'=>'.$act);
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

function toHTML($s) {
	$s = str_replace('&', '&amp;', $s);
    $s = str_replace('ñ', '&ntilde;', $s);
    $s = str_replace('¿', '&iquest;', $s);
    $s = str_replace('Ñ', '&Ntilde;', $s);
    $s = str_replace('á', '&aacute;',  $s);
    $s = str_replace('é', '&eacute;', $s);
    $s = str_replace('í', '&iacute;', $s);
    $s = str_replace('ó', '&oacute;', $s);
    $s = str_replace('ú', '&uacute;', $s);
    $s = str_replace('Á', '&Aacute;',$s);
    $s = str_replace('É', '&Eacute;', $s);
    $s = str_replace('Í', '&Iacute;', $s);
    $s = str_replace('Ó', '&Ooacute;',$s);
    $s = str_replace('Ú', '&Uacute;', $s);

	$s = htmlentities($s);
    return $s;
   //return mb_convert_encoding($s,"HTML-ENTITIES","auto");
}
function toXML($s) {
    $s = str_replace('&', '&amp;', $s);
    $s = ereg_replace('&(amp;|&amp;)+(([A-Za-z0-9#]+);)','&\\2', $s);
    $s = str_replace('>', '&#62;', $s);
    $s = str_replace('&gt;', '&#62;', $s);
    $s = str_replace('<', '&#60;', $s);
    $s = str_replace('&lt;', '&#60;', $s);
    $s = str_replace('"', '&#34;', $s);
    //$s = str_replace('|', '&#166;', $s);
    $s = str_replace('&amp;', '&#38;', $s);
    $s = str_replace('&iquest;','&#191;', $s);
    $s = str_replace('&aacute;', '&#225;', $s);
    $s = str_replace('&eacute;', '&#233;', $s);
    $s = str_replace('&iacute;', '&#237;', $s);
    $s = str_replace('&oacute;', '&#243;', $s);
    $s = str_replace('&uacute;', '&#250;', $s);
    $s = str_replace('&uuml;', '&#252;', $s);
    $s = str_replace('&ntilde;', '&#241;', $s);
    $s = str_replace('&Aacute;', '&#193;', $s);
    $s = str_replace('&Eacute;', '&#201;', $s);
    $s = str_replace('&Iacute;', '&#205;', $s);
    $s = str_replace('&Oacute;', '&#211;', $s);
    $s = str_replace('&Uacute;', '&#218;', $s);
    $s = str_replace('&Uuml;', '&#220;', $s);
    $s = str_replace('&Ntilde;', '&#209;', $s);

    $s = str_replace('&quote;', '&#34;', $s);
    $s = str_replace('&nbsp;','&#160;', $s);
	$s = ereg_replace('&([A-Za-z0-9]+);','&#38;\\1;', $s);
    return $s;
}

function toAjax($s){
	return	toXML(toHTML($s));
}

function lambda( $args, $code, $env=array() ) {
   static $n = 0;
   $functionName = sprintf('ref_lambda_%d',++$n);
   $_SESSION['lambdas'][$functionName]['environment_vars'] =& $env;
   $declaration = sprintf('function &%s(%s) {extract($_SESSION["lambdas"]["'.$functionName.'"]["environment_vars"],EXTR_REFS); ' /*.'trigger_error(backtrace_string(\''.str_replace('\'','\\\'',$code).'\'), E_USER_NOTICE);' */.'%s}',$functionName,$args,$code);
   eval($declaration);
   return $functionName;
}

function delete_lambda($name){
	unset($_SESSION['lambdas'][$name]);
}


function isPWBObject(&$e){
	return is_object($e) && isset($e->isClassOfPWB);
}

function &apply_messages(&$u, $mess){
	$temp =& $u;
	$ms = explode('->',$mess);
	foreach($ms as $m){
		$temp =& apply_message($temp,$m);
	}
	return $temp;
}

function &apply_message(&$u, $mess){
	if (substr($mess,-2)=='()'){
		$m = substr($mess,0,-2);
		return $u->$m();
	} else {
		return $u->$mess;
	}
}

function getClass(&$o){
	return strtolower(get_class($o));
}

if (version_compare(phpversion(), '5.0') < 0) {
    eval('
    function clone($object) {
      return $object;
    }
    ');
}

function try_catch($try_code, $catch_matches) {
	$maybe_exception = $try_code();
	if (!is_exception($maybe_exception))
		return $maybe_exception;

	foreach ($catch_matches as $catch_exception => $catch_code) {
		if (matches_exception($maybe_exception, $catch_exception)) {
			return $catch_code($maybe_exception);
		}
	}
}

function &newAnnonymous($class_name, $body) {
	$annon_class_name = nextAnnonymousClassName();
	eval("class $annon_class_name extends $class_name $body");
	return new $annon_class_name;
}

function nextAnnonymousClassName() {
	if (!isset($_SESSION['next_annonimous_class'])) {
		$_SESSION['next_annonimous_class'] = 0;
	} else {
		$_SESSION['next_annonimous_class'] += 1;
	}
	return 'annon_class_' . $_SESSION['next_annonimous_class'];
}

// Call with multiple dispatch

function &mdcall($function) {
	$c = array();
	$n = func_num_args() - 1;
	for ($i = 1; $i <= $n ; $i++) {
		$argi =& func_get_arg($i);
		if (is_object($argi)) {
			$c[$i] = get_class($argi);
		}
		else {
			$c[$i] = gettype($argi);
		}
	}

	$fname = _mdcall($function, $c, 1);

	if ($fname != null) {
		$args =& func_get_args();
		$params = array();
		for($i = 1; $i <= $n; $i++) {
			$params[$i] = '$args[' . $i . ']';
		}

		//echo 'Evaluating: ' . '$res =& ' . $fname . '(' . implode(',', $params) . ');';
		eval('$res =& ' . $fname . '(' . implode(',', $params) . ');');
		return $res;
	} else {
		print_backtrace('Dispatch failed');
	}
}

function _mdcall($function, $arg_types, $i) {
	$n = count($arg_types);
	if ($i > $n) {
		$fname = $function;
		for ($j = 1; $j <= $n; $j++) {
			$fname .= '_' . $arg_types[$j];
		}

		//echo 'Checking for ' . $fname . '</br>';
		if (function_exists($fname)) {
			return $fname;
		}
		else {
			return null;
		}
	}
	else {
		$fname = _mdcall($function, $arg_types, $i + 1);
		$parent = get_parent_class($arg_types[$i]);
		while (($fname == null) and $parent) {
			$arg_types[$i] = $parent;
			$fname = _mdcall($function, $arg_types, $i + 1);
			$parent = get_parent_class($arg_types[$i]);
		}

		return $fname;
	}
}
?>