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
		foreach (getfilesrec($lam = lambda('$file', '$v=substr($file, -4)==".php";return $v;', $a = array ()), $file) as $f) {
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

function print_backtrace_and_exit($error) {
	print_backtrace($error);
	echo '<br />';
	echo '<a href=' . site_url .'?restart=yes>Restart application</a>';
	exit;
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
 * Encodes the string una ajax suitable manner
 */

function toAjax($s) {
	$app =&Application::instance();
	return $app->page_renderer->toAjax($s);
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

function exceptions_enabled() {
	return (defined('exceptions') and (constant('exceptions') == 1));
}

function is_exception(&$ex) {
	return (is_a($ex,'PWBException')) or
	       (is_a($ex, 'Exception'));
}


/***************************/
/** Error handler **********/
/***************************/

function fatal_error_handler($buffer) {
	if (ereg("(error</b>:)(.+)(<br)", $buffer, $regs)) {
		$err = preg_replace("/<.*?>/", "", $regs[2]);
		error_log($err);

		if (defined('error_handler') and constant('error_handler') == 'disabled') {
			$buffer .= backtrace_string();
			$buffer .= '<br /><a href="' . site_url . '?restart=yes">Restart application</a>';
			return $buffer;
		}

		// The following is copied from Session.php without understanding much :S
		// Programming by instinct :P
		SessionHandler::setHooks();
		session_name(strtolower('BugNotifierApplication'));
		$sessionid = $_COOKIE[session_name()];
  		$orgpath = getcwd();
  		@chdir(PHP_BINDIR);
  		@chdir(session_save_path());
  		$path = realpath(getcwd()).'/';
  		if(file_exists($path.'sess_'.$sessionid)) {
   			@unlink($path.'sess_'.$sessionid);
  		}
  		@chdir($orgpath);
  		session_start();
  		session_destroy();
  		SessionHandler::setHooks();
  		session_regenerate_id();
		session_start();
		unset($_SESSION[sitename][app_class]);

		$app = & new BugNotifierApplication;
		$app->setError($err);
		$app->setBacktrace($buffer);

		return $app->render();
	}
	return $buffer;
}

function handle_error($errno, $errstr, $errfile, $errline) {
	//error_log("$errstr in $errfile on line $errline");
	//print_backtrace($errno);
	if ($errno == 'FATAL' || $errno == 'ERROR') {
		echo "error</b>:<br/>";
		print_backtrace();
		ob_end_flush();
		echo "ERROR CAUGHT check log file";
		exit (0);
	}
}

?>