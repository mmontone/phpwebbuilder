<?php
require_once 'md.php';
//require_once 'md2.php';

/**
 * Some basic functions.
 */

function includeAll() {

	if (!defined('modules')) {
		define('modules', "Core,Application,Model,Instances,View,database,DefaultCMS,QuicKlick,DrPHP,BugNotifier,Logging");
	}
	if (!defined('app_class')) {
		define('app_class', "DefaultCMSApplication");
	}

	$modules = explode(",", modules);
	$modules[] = 'Logging';
	includeAllModules(pwbdir, modules);
	define('app', "MyInstances,MyComponents");
	includeAllModules(basedir, app);
	includeAllModules(pwbdir, 'Session');

}
function includeAllModules($prefix, $modules) {
	foreach (explode(",", $modules) as $dir) {
		includemodule($prefix . '/' . trim($dir));
	}
}

/**
 * Gets all the files in a directory tree that matches the condition
 */
function getfilesrec($pred, $dir) {
	if (is_dir($dir)) {
		$ret = array ();
		$gestor = opendir($dir);
		while (false !== ($f = readdir($gestor))) {
			if (substr($f, -1) != '.')
				$ret = array_merge(getfilesrec($pred, implode(array (
					$dir,
					'/',
					$f
				))), $ret);
		}
		return $ret;
	} else {
		if ($pred ($dir)) {
			return array (
				$dir
			);
		} else {
			return array ();
		}
	}
}

function getfiles($pred, $dir) {
	if (is_dir($dir)) {
		$ret = array ();
		$gestor = opendir($dir);
		while (false !== ($f = readdir($gestor))) {
			if ((!is_dir($dir . '/' . $f)) and (substr($f, -1) != '.')) {
				array_push($ret, $dir . '/' . $f);
			}
		}
		return $ret;
	} else {
		if ($pred ($dir)) {
			return array (
				$dir
			);
		} else {
			return array ();
		}
	}
}
/**
 * Includes all php files from a directory tree
 */
function includefile(& $file) {
		foreach (getfilesrec($lam = lambda('$file', 'return $v=substr($file, -4)==".php";', $a = array ()), $file) as $f) {
		require_once ($f);
	}
	delete_lambda($lam);
}
/**
 * Includes a PWB Module
 */
function includemodule($module) {
	$modf = implode(array (
		$module,
		'/',
		basename($module
	), '.php'));
	if (file_exists($modf)) {
		require_once ($modf);
	} else {
		trigger_error('Falling back to includefile for ' . $module, E_USER_NOTICE);
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
function trace_params() {
	foreach ($_REQUEST as $name => $value)
		trace($name . "=" . $value . "<BR>");
}

/**
 * The array containing all classes and their subclasses
 */
$PWBclasses = array ();
/**
 * Finds all the subclases for the specified class (works only for PWB objects!)
 */
function find_subclasses() {
	global $PWBclasses;
	$arr = get_declared_classes();
	$ret = array ();
	foreach ($arr as $o) {
		$vars = get_class_vars($o);
		if (isset ($vars["isClassOfPWB"]) && $vars["isClassOfPWB"]) {
			$PWBclasses[strtolower($o)] = array ();
			$pcs = get_superclasses($o);
			foreach ($pcs as $pc) {
				$PWBclasses[$pc][] = $o;
			}
		}
	}
}
/**
 * Returns the subclasses of the specified class, in higher-to-lower order
 */
function get_subclasses($str) {
	global $PWBclasses;
	if (count($PWBclasses) == 0)
		find_subclasses();
	return $PWBclasses[strtolower($str)];
}
/**
 * Returns the subclasses of the specified class, in lower-to-higher order
 */
function get_superclasses($str) {
	global $PWBclasses;
	$ret = array ();
	$pc = get_parent_class($str);
	while ($pc != '') {
		$ret[] = strtolower($pc);
		$pc = get_parent_class($pc);
	}
	return $ret;
}

/**
 * Returns all the classes that are related to the parameter (inheritance-wise)
 */
function get_related_classes($str) {
	return array_merge(get_superclasses($str), get_subclasses($str));
}
/**
 * This function checks if the user with $id id, has the permission $permission
 */
function fHasPermission($id, $permission) {
	$u = & User :: logged();
	return $u->hasPermission($permission);
}

/**
 * This function checks if the user with $id id, has the permission
 * for the action $act on the object $obj.
 */
function fHasAnyPermission($id, $obj, $act) {
	return fHasPermission($id, '*') || fHasPermission($id, '*=>' . $act) || fHasPermission($id, $obj . '=>*') || fHasPermission($id, $obj . '=>' . $act);
}

/**
 * prints the backtrace.
 */
function backtrace() {
	print_r(debug_backtrace());
}
/**
 * prints the backtrace.
 */
function print_backtrace($error) {
	echo backtrace_string($error);
}
/**
 * Creates a text representation of the backtrace
 */
function backtrace_string($error) {
	$back_trace = debug_backtrace();
	$ret = "<h1>$error</h1>";
	foreach ($back_trace as $trace) {
		$ret .= "<br/><b> {$trace['file']}: {$trace['line']} ({$trace['function']})</b>";
		//$ret .= print_r($trace['args'], TRUE);
	}
	return $ret;
}

function backtrace_plain_string($error) {
	$back_trace = debug_backtrace();
	$ret = "Error: $error\n";
	$ret .= "Backtrace: ";
	foreach ($back_trace as $trace) {
		$ret .= "\n{$trace['file']}: {$trace['line']} ({$trace['function']})";
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

/**
 * Assert handler function
 */
function my_assert_handler($file, $line, $code) {
	echo "<hr>Assertion Failed:
	       <b>File</b> '$file'<br />
	       <b>Line</b> '$line'<br />
	       <b>Code</b> '$code'<br />";
	print_backtrace('');
	echo "</hr>";
}

// Set up the callback
assert_options(ASSERT_CALLBACK, 'my_assert_handler');

/**
 * encodes the string to valid XHTML
 */

function toHTML($s) {
	$s = str_replace('&', '&amp;', $s);
	$s = str_replace('ñ', '&ntilde;', $s);
	$s = str_replace('¿', '&iquest;', $s);
	$s = str_replace('Ñ', '&Ntilde;', $s);
	$s = str_replace('á', '&aacute;', $s);
	$s = str_replace('é', '&eacute;', $s);
	$s = str_replace('í', '&iacute;', $s);
	$s = str_replace('ó', '&oacute;', $s);
	$s = str_replace('ú', '&uacute;', $s);
	$s = str_replace('Á', '&Aacute;', $s);
	$s = str_replace('É', '&Eacute;', $s);
	$s = str_replace('Í', '&Iacute;', $s);
	$s = str_replace('Ó', '&Ooacute;', $s);
	$s = str_replace('Ú', '&Uacute;', $s);

	$s = htmlentities($s);
	return $s;
	//return mb_convert_encoding($s,"HTML-ENTITIES","auto");
}

/**
 * Encodes the string in valid XML
 */

function toXML($s) {
	$s = str_replace('&', '&amp;', $s);
	$s = ereg_replace('&(amp;|&amp;)+(([A-Za-z0-9#]+);)', '&\\2', $s);
	$s = str_replace('>', '&#62;', $s);
	$s = str_replace('&gt;', '&#62;', $s);
	$s = str_replace('<', '&#60;', $s);
	$s = str_replace('&lt;', '&#60;', $s);
	$s = str_replace('"', '&#34;', $s);
	//$s = str_replace('|', '&#166;', $s);
	$s = str_replace('&amp;', '&#38;', $s);
	$s = str_replace('&iquest;', '&#191;', $s);
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
	$s = str_replace('&nbsp;', '&#160;', $s);
	$s = ereg_replace('&([A-Za-z0-9]+);', '&#38;\\1;', $s);
	return $s;
}
/**
 * Encodes the string una ajax suitable manner
 */

function toAjax($s) {
	return toXML(toHTML($s));
}
/**
 * Receives the definition of the parameters, the definition of the body,
 * and a context (an array of name=>variable to be used) and creates an
 * anonymous function. The context can be obtained with get_defined_vars()
 */
function lambda($args, $code, $env = array ()) {
	static $n = 0;
	$functionName = sprintf('ref_lambda_%d', ++ $n);
	$_SESSION['lambdas'][$functionName]['environment_vars'] = & $env;
	$declaration = sprintf('function &%s(%s) {extract($_SESSION["lambdas"]["' . $functionName . '"]["environment_vars"],EXTR_REFS); ' /*.'trigger_error(backtrace_string(\''.str_replace('\'','\\\'',$code).'\'), E_USER_NOTICE);' */
	 . '%s}', $functionName, $args, $code);
	eval ($declaration);
	return $functionName;
}
/**
 * Frees the space used for the variable's context
 */
function delete_lambda($name) {
	unset ($_SESSION['lambdas'][$name]);
}

/**
 * Checks if the variable references a PWB object
 */
function isPWBObject(& $e) {
	return is_object($e) && isset ($e->isClassOfPWB);
}

/**
 * Applies a sequence of messages and accessors
 * to the parameter. See apply_message
 * Example: apply_messages(User::logged(), 'name->getValue()'){
 */
function & apply_messages(& $u, $mess) {
	$temp = & $u;
	$ms = explode('->', $mess);
	foreach ($ms as $m) {
		$temp = & apply_message($temp, $m);
	}
	return $temp;
}

/**
 * Applies a message or accessor
 * to the parameter, depending on parenthesis use.
 */
function & apply_message(& $u, $mess) {
	if (substr($mess, -2) == '()') {
		$m = substr($mess, 0, -2);
		return $u-> $m ();
	} else {
		return $u-> $mess;
	}
}

/**
 * Returns the class name in a PHP4 and 5 compatible way
 */
function getClass(& $o) {
	return strtolower(get_class($o));
}

/**
 * Clone function for php4/5 compatibility
 */
if (version_compare(phpversion(), '5.0') < 0) {
	eval ('
	    function clone($object) {
	      return $object;
	    }
	    ');
}

/***************************/
/** Error handler **********/
/***************************/

function fatal_error_handler($buffer) {
	if (defined('error_handler') and constant('error_handler') == 'disabled') {
		return $buffer;
	}

	if (ereg("(error</b>:)(.+)(<br)", $buffer, $regs)) {
		$err = preg_replace("/<.*?>/", "", $regs[2]);
		error_log($err);
		$app = & new BugNotifierApplication;
		$app->setError($err);
		$app->setBacktrace(backtrace_plain_string($err));
		return $app->render();
	}
	return $buffer;
}

function handle_error($errno, $errstr, $errfile, $errline) {
	error_log("$errstr in $errfile on line $errline");
	if ($errno == FATAL || $errno == ERROR) {
		ob_end_flush();
		echo "ERROR CAUGHT check log file";
		exit (0);
	}
}
?>