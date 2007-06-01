<?php


//require_once 'md2.php';
if (!defined('md')) {define('md','compiled');}
require_once 'md.php';
//}

//require_once 'query_lang.php';
require_once 'Compiler.class.php';

//require_once 'md2.php';

/**
 * Some basic functions.
 */

#@defmacro defmd@#
function defmd($text) {
	// We use template language to parse macros input??
	$out = & parse_with('$string {name} "(" $params {params} ")"' .
	'$param = $string {arg_name} ":" $string {arg_type}' .
	'$params = $param' .
	'$params = $param "," $params');
}

$cache_var = 1;
function get_cache_var() {
	global $cache_var;
	return '$this->cachevar_' . $cache_var++;
}

function getcached($text) {
	preg_match('/(.*)[\s\t]*\{(.*)\}/s', $text, $matches);
	$params = $matches[1];
	$body = $matches[2];

	$vars = array ();
	$ps = explode(',', $params);
	foreach ($ps as $p) {
		$paramspec = trim($p);
		$paramspec = explode('=>', $paramspec);
		$vars[trim($paramspec[0])] = trim($paramspec[1]);
	}

	if (isset ($vars['var'])) {
		$var = $vars['var'];
	} else {
		$var = get_cache_var();
	}

	$initialize = $vars['initialize'];

	if (isset ($vars['result'])) {
		$result = $vars['result'];
	} else {
		$result = $initialize;
	}

	$out = "if (is_null($var)) {\n
	    	       $body\n
	               $var =& $initialize;\n
	           }\n
	           $result =& $var;";
	return $out;
}

$gensym = 0;
function gensym() {
	global $gensym;

	return 'sym' . $gensym++;
}

function sexp_parser($text) {

}

function yaml_parser($text) {
	return Spyc :: YAMLLoad($text);
}

function lambda_parser($text) {
	return sexp_parser($text);
}

function preprocessor($code) {
	return eval ($code);
}

$mixins = array ();
$_mixin_file = array ();
#@mixin MyMixin
{
	function mixedFunc() {
		echo 'Hola';
	}
} //@#

function mixin($text) {
	preg_match('/([[:alpha:]]*)\s*\{(.*)\}/s', $text, $matches);
	$name = $matches[1];
	//echo 'Mixin name: ' . $name . '<br/>';
	$body = $matches[2];
	//echo 'Mixin body: ' . $body . '<br/>';
	global $mixins;
	$mixins[$name] = preg_replace("/\n|\r/", '', $body);
	//$mixins[$name] = $body;
	return 'global $_mixin_file;$_mixin_file[\'' . $name . '\']=\'' . Compiler :: actualFile() . '\';';
}
/*
 class Mixed {
 #@use_mixin MyMixin, OtherMixin@#

 }
 $m =& new Mixed;
 $m->mixedFunc();
*/

function use_mixin($text) {
	$ms = explode(',', $text);
	global $mixins;
	$code = '';
	foreach ($ms as $name) {
		//echo 'UseMixin name: ' . $name . '<br/>';
		$name = trim($name);
		if (isset ($mixins[$name])) {
			$code .= 'var $__use_mixin_' . $name . '=true;';
			$code .= $mixins[$name];
		} else {
			global $_mixin_file;
			$comp = & Compiler :: instance();
			$comp->compileFile($_mixin_file[$name]);
			if (isset ($mixins[$name])) {
				$code .= use_mixin($name);
				break;
			}
			print_r($mixins);
			print_backtrace_and_exit('Mixin ' . $name . ' not defined');
		}
	}
	return $code;
}

function optionalCompile($tag, $code) {
	if (Compiler :: CompileOpt($tag) || defined($tag)) {
		return $code;
	} else {
		return preg_replace('/[^\n]/', '', $code);
	}
}

function php5($code) {
	return optionalCompile('php5', $code);
}

function model_events_echo($code) {
	return optionalCompile('model_events_echo', $code);
}
function php4($code) {
	if (!defined('php5')) {
		return $code;
	} else {
		return '';
	}
}

function debugview($text) {
	return optionalCompile('debugview', $text);
}

function debug_report($text) {
	return optionalCompile('debug_report', $text);
}

function profile($text) {
	return optionalCompile('profile', $text);
}

function sql_echo($text) {
	return optionalCompile('sql_echo', $text);
}

function tm_echo($text) {
	return optionalCompile('tm_echo', $text);
}

function activation_echo($text) {
	return optionalCompile('activation_echo', $text);
}

function calling_echo($text) {
    return optionalCompile('calling_echo', $text);
}

function sql_echo1($text) {
	if (defined('sql_echo') and (constant('sql_echo') >= 1)) {
		return $text;
	}
}

function sql_echo2($text) {
	if (defined('sql_echo') and (constant('sql_echo') >= 2)) {
		return $text;
	}
}

function persistence_echo($text) {
	return optionalCompile('persistence_echo', $text);
}

function track_events($text) {
	return optionalCompile('track_events', $text);
}

function track_events1($text) {
	if (defined('track_events') and constant('track_events') >= 1) {
		return $text;
	}
}

function track_events2($text) {
	if (defined('track_events') and constant('track_events') >= 2) {
		return $text;
	}
}

function track_events3($text) {
	if (defined('track_events') and constant('track_events') >= 3) {
		return $text;
	}
}

#@check $x>$y@#
function check($text) {
	return optionalCompile('assertions', "assert('" . addslashes($text) . "');\n");
}

function gencheck($text) {
	return optionalCompile('assertions', $text);
}

#@typecheck $t : PWBObject, $s : Component@#
function typecheck($text) {
	if (Compiler :: CompileOpt('typechecking')) {
		$code = '';
		$params = explode(',', $text);
		foreach ($params as $param) {
			$case = explode(':', $param);
			$arg = trim($case[0]);
			$type = trim($case[1]);
			//$code .= "assert('is_a(".addslashes($arg).", \'".addslashes($type)."\')');\n";
			$escaped_arg = addslashes($arg);
			$code .= "if (!hasType($arg,'$type')) {" .
			"print_backtrace('Type error. Argument: $escaped_arg. Type: ' . getTypeOf($arg) . '. Expected: $type');}";
		}
		return $code;
	} else {
		return '';
	}
}

function hasType($arg, $type) {
	if (is_object($arg)) {
		if ($type == 'object')
			return true;
		if (method_exists($arg, 'hasType')) {
			return $arg->hasType($type);
		} else {
			return is_a($arg, $type);
		}
	} else {
		return !strcasecmp(gettype($arg), $type);
	}
}

function getTypeOf($arg) {
	if (is_object($arg)) {
		return getClass($arg);
	} else {
		return gettype($arg);
	}
}

function addsimplequoteslashes($body) {
	return str_replace('\'', '\\\'', $body);
}

#@lam $x,$y ->return $x + $y;@#
function lam($text) {
	//echo 'Trying to lam: ' . $text;

	$matches = explode('->', $text, 2);
	$params = $matches[0];
	$body = $matches[1];
	$t = "lambda('$params','" . addsimplequoteslashes($body) . "', get_defined_vars())";
	return $t;
}

$dyn_vars = array ();

function defdyn($var, & $value) {
	global $dyn_vars;
	if (!is_array($dyn_vars[$var])) {
		$dyn_vars[$var] = array ();
	}

	array_push($dyn_vars[$var], $value);
}

function undefdyn($var) {
	global $dyn_vars;
	array_pop($dyn_vars[$var]);
}

function & getdyn($var) {
	global $dyn_vars;
	$arr = $dyn_vars[$var];
	return $arr[count($arr) - 1];
}
/*
#@dlet a=array(), c=& new MyObject
  {
	print_r(getdyn('c'));
  }//@#
*/
function dlet($text) {
	preg_match('/(.*)[\s\t]*\{(.*)\}/s', $text, $matches);
	$vars = $matches[1];
	$body = $matches[2];

	$code = '';
	$vs = explode(',', $vars);
	$vss = array ();
	foreach ($vs as $v) {
		preg_match('/(.*)\s*=\&?\s*(.*)/', $v, $vmatches);
		$var = trim($vmatches[1]);
		$vss[] = & $var;
		$value = trim($vmatches[2]);
		$code .= "defdyn($var, $value);\n";
	}

	$code .= $body;

	foreach ($vss as $v) {
		$code .= "\nundefdyn($v);";
	}

	return $code;
}

function defmacro($text) {

}

function deprecated($text) {
	return '';
}

function getIncludes() {
	$modules = eval ('return modules;');
	$app = eval ('return app;');
	$inc = '';
	$inc .= includeAllModules(pwbdir, $modules);
	$inc .= includeAllModules(basedir, $app);
	$inc .= includeAllModules(pwbdir, 'Session');
	return $inc;
}

function includeAll() {
	if (!defined('modules')) {

		define('modules', "Core,Application,DbgMode,Model,YATTAA,Instances,View,database,DefaultCMS,QuicKlick,DrPHP,BugNotifier,Logging");

	}

	if (!defined('app_class')) {
		define('app_class', "DefaultCMSApplication");
	}
	define('app', "MyInstances,MyComponents");

	if (Compiler :: CompileOpt('recursive') || Compiler :: CompileOpt('optimal') || Compiler :: CompileOpt('minimal')) {
		$comp = & Compiler :: Instance();
		$file = $comp->getTempDir('') . strtolower(constant('app_class')) . '.php';
		if (!file_exists($file)) {
			$_REQUEST['recompile'] = 'yes';
		}
		if (isset ($_REQUEST['recompile'])) {
			$fo = fopen($file, 'w');
			$f = '<?php ' . getIncludes() . ' ?>';
			fwrite($fo, $f);
			fclose($fo);
		}
		$comp->compile($file);
		//$comp->compiled = array();
	} else {
		eval (getIncludes());
	}
	require_once pwbdir . 'Session/SessionStart.php';

	if (isset ($_REQUEST['recompile'])) {
		$temp_file = ViewCreator :: getTemplatesFilename();
		@ unlink($temp_file);
	}
}

function includeAllModules($prefix, $modules) {
	$ret = '';
	foreach (explode(",", $modules) as $dir) {
		$ret .= includemodule($prefix . trim($dir));
	}
	return $ret;
}

/**
 * Gets all the files in a directory tree that matches the condition
 */
function getfilesrec($pred, $dir) {
	if (is_dir($dir)) {
		$ret = array ();
		$gestor = opendir($dir);
		while (false !== ($f = readdir($gestor))) {
			if ($f != '.' && $f != '..')
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
function includefile($file) {
	$ret = '';
		foreach (getfilesrec(lambda('$file', '$v=substr($file, -4)==".php";return $v;', $a = array ()), $file) as $f) {
		$ret .= "compile_once ('$f');";
	}
	return $ret;
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
		return "compile_once ('$modf');";
	} else {
		//trigger_error('Falling back to includefile for ' . $module, E_USER_NOTICE);
		return includefile($module);
	}
}
/*returns the dir inside the pwbdir*/
function getDirName($file) {
	//return pwbdir.substr(dirname($file), strlen(dirname(dirname(__FILE__)))+1);
	return dirname($file);
}

function debug($str) {
	$app = & Application :: Instance();
	$app->page_renderer->debug($str);
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

function reset_metadata() {
	$GLOBALS['persistentObjectsMetaData'] = array ();
}

function find_metadata() {
	$GLOBALS['allRelatedClasses'] = array ();
	find_subclasses();
	$GLOBALS['persistentObjectsMetaData'] = array ();
	foreach (get_subclasses('PersistentObject') as $sc) {
		if (Compiler :: classCompiled($sc)) {
			PersistentObjectMetaData :: getMetaData($sc);
		}
	}
	//print_backtrace(strlen(serialize($GLOBALS['persistentObjectsMetaData'])));
	$ret = '$GLOBALS[\'allRelatedClasses\']=unserialize(\'' . addsimplequoteslashes(serialize($GLOBALS['allRelatedClasses'])) . '\');';
	$ret .= '$GLOBALS[\'persistentObjectsMetaData\']=unserialize(\'' . addsimplequoteslashes(serialize($GLOBALS['persistentObjectsMetaData'])) . '\');';
	return $ret;
}

/**
 * Finds all the subclases for the specified class (works only for PWB objects!)
 */
function find_subclasses() {
	$PWBclasses = & get_allRelatedClasses();
	$arr = get_declared_classes();
	$ret = array ();
	foreach ($arr as $o) {
		$vars = get_class_vars($o);
		if (isset ($vars["isClassOfPWB"]) && Compiler :: classCompiled($o)) {
			$PWBclasses[strtolower($o)] = array ();
			$pcs = get_superclasses($o);
			foreach ($pcs as $pc) {
				$PWBclasses[$pc][] = strtolower($o);
			}
		}
	}
}

function & get_allRelatedClasses() {
	return getSessionGlobal('allRelatedClasses');
}

function & getSessionGlobal($name) {
	if (!isset ($GLOBALS[$name])) {
		$GLOBALS[$name] = & Session :: getAttribute($name);
	}
	return $GLOBALS[$name];
}

/**
 * Returns the subclasses of the specified class, in higher-to-lower order
 */
function get_subclasses($str) {
	$PWBclasses = & get_allRelatedClasses();
	if ($PWBclasses == null)
		find_subclasses();
	$sub = @ $PWBclasses[strtolower($str)];
	if ($sub === null) {
		return array ();
	} else {
		return $sub;
	}
}

function get_subclasses_and_class($class) {
	$arr = get_subclasses($class);
	$arr[] = strtolower($class);
	//array_unshift($arr, strtolower($class));
	return $arr;
}

function sp2nbsp($str) {
	return str_replace(' ', '&nbsp;', str_replace("\t", '    ', $str));
}
function htmlshow($str) {
	echo (nl2br(sp2nbsp(htmlentities($str))));
}

function is_subclass($class_sub, $class_parent) {
	$class_sub = preg_replace('/\<\w*\>/','',$class_sub);
	$class_parent = preg_replace('/\<\w*\>/','',$class_parent);
	return in_array(strtolower($class_sub), get_subclasses_and_class($class_parent));
}
function is_strict_subclass($class_sub, $class_parent) {
	return in_array(strtolower($class_sub), get_subclasses($class_parent));
}

/**
 * Returns the subclasses of the specified class, in lower-to-higher order
 */
function get_superclasses($str) {
	return get_superclasses_upto($str, '');
}
function get_superclasses_upto($str, $class) {
	$ret = array ();
	$class = strtolower($class);
	$pc = strtolower(get_parent_class($str));
	while ($pc != $class && $pc != '') {
		$ret[] = $pc;
		$pc = strtolower(get_parent_class($pc));
	}
	if ($pc !== $class) {
		return array ();
	} else {
		return $ret;
	}
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
function print_backtrace($error = '') {
	echo backtrace_string($error);

}

function print_backtrace_and_exit($error = '') {
	print_backtrace($error);
	echo '<br />';
	echo '<a href=' . site_url . '?restart=yes>Restart application</a>';
	exit;
}
/**
 * Creates a text representation of the backtrace
 */
function backtrace_string($error, $sep = '<br/>') {
	$ret = "<h1>$error</h1>";
	foreach (debug_backtrace() as $trace) {
		$ret .= $sep . "<b> " . @ $trace['file'] . ": " . @ $trace['line'] . " ({$trace['function']})</b>";
	}
	return $ret;
}
function get_global_debug() {
	echo '<h1>' .
	'trace size: ' . count(debug_backtrace()) .
	'memory size: ' . memory_get_usage() .
	'</h1>';

	print_backtrace('');
	/*foreach (array('allObjectsInMem','lambda_vars') as $key) {
	     echo "$key=";
	     echo strlen(serialize($GLOBALS[$key]));
	 }*/

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
	$app = & Application :: instance();
	return $app->page_renderer->toAjax($s);
}
/**
 * Receives the definition of the parameters, the definition of the body,
 * and a context (an array of name=>variable to be used) and creates an
 * anonymous function. The context can be obtained with get_defined_vars()
 */

$lambda_vars = array ();

function lambda($args, $code, $env = array ()) {
	static $n = 0;
	$functionName = 'ref_lambda_' . (++ $n);
	$GLOBALS['lambda_vars'][$functionName] = & $env;
	//$declaration = implode('', array('function &',$functionName,'(',$args,') {extract($GLOBALS[\'lambda_vars\'][\'',$functionName,'\'],EXTR_REFS); ',$code,'}'));
	$declaration = 'function &' . $functionName . '(' . $args . ') {extract($GLOBALS[\'lambda_vars\'][\'' . $functionName . '\'],EXTR_REFS); ' . $code . '}';
	//$ok =
	eval ($declaration);
	//if($ok===FALSE /* or ! preg_match('/return(\s)*\$\w+;/s',$code)*/)
	//print_backtrace($args.'==>'.$code);
	return $functionName;
}
/**
 * Frees the space used for the variable's context
 */
function delete_lambda($name) {
	unset ($GLOBALS['lambda_vars'][$name]);
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
		return $u->$m ();
	} else {
		return $u->$mess;
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

function is_exception(& $ex) {
	return (is_a($ex, 'PWBException')) or (is_a($ex, 'Exception'));
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
		SessionHandler :: setHooks();
		session_name(strtolower('BugNotifierApplication'));
		$sessionid = $_COOKIE[session_name()];
		$orgpath = getcwd();
		@ chdir(PHP_BINDIR);
		@ chdir(session_save_path());
		$path = realpath(getcwd()) . '/';
		if (file_exists($path . 'sess_' . $sessionid)) {
			@ unlink($path . 'sess_' . $sessionid);
		}
		@ chdir($orgpath);
		session_start();
		session_destroy();
		SessionHandler :: setHooks();
		session_regenerate_id();
		session_start();
		Session :: removeAttribute(app_class);

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
	if ((strpos($errstr, 'Only variables') !== FALSE) && Compiler :: CompileOpt('assertions')) {
		global $last_lambda;
		//print_r(array_slice($last_lambda, -10));
		print_backtrace('REFERENCE ERROR!' . $errstr);
	}
	if ($errno == E_ERROR) {
		echo "error</b>:<br/>";
		print_backtrace();
		ob_end_flush();
		echo "ERROR CAUGHT check log file";
		exit (0);
	} else
		if (ini_get('error_reporting') & $errno) {
			print_backtrace($errno . $errstr);
		}
}

//set_error_handler('handle_error');

function print_n($obj, $n = 5) {
	if ($n != 0) {
		if (is_array($obj)) {
			$ret = 'Array(';
			foreach ($obj as $i => $o) {
				$ret .= $i . '=>' . print_n($o, $n -1);
			}
			return $ret . ')';
		}
		if (is_object($obj)) {
			$ret = 'Object(';
			foreach ($obj as $i => $o) {
				$ret .= $i . '=>' . print_n($o, $n -1);
			}
			return $ret . ')';
		}
		return gettype($obj) . ':' . $obj;
	}
}

function print_object($array, $extra = '') {
	if (is_array($array)) {
		/*
		$printed_elements = array();
		foreach (array_keys($array) as $i) {
		    $elem =& $array[$i];

		    $printed_elements[] = print_object($elem);
		}

		return 'array(' . implode(',', $printed_elements) .')';*/
		return '[array size: ' . count($array) . $extra . ']';
	} else {
		if (is_a($array, 'pwbobject')) {
			return $array->primPrintString($extra);
		} else {
			if (is_null($array)) {
				return 'null' . $extra;
			} else {
				if (is_bool($array)) {
					if ($array) {
						return 'true' . $extra;
					} else {
						return 'false' . $extra;
					}
				} else {
					if (is_object($array)) {
						#@php4
                        $r = '[' . getClass($array) . ':' . get_primitive_object_id($array) . $extra . ']';
                        //@#

                        #@php5
                        //$r = '[' . getClass($array) . ':' . spl_object_hash($array)  .' ]';
                        $r = '[' . getClass($array) . ':' . get_primitive_object_id($array) . $extra . ']';
                        //@#

                        return $r;
                    } else {
						return print_r($array, true);
					}
				}
			}
		}
	}
}

function get_primitive_object_id(&$object) {
	ob_start();
    print($object);
    $c = ob_get_contents();
    $c = substr($c, strlen('Object id #'));
    ob_end_clean();
    return $c;
}

function array_union_values() {
	//echo func_num_args();  /* Get the total # of arguements (parameter) that was passed to this function... */
	//print_r(func_get_arg());  /* Get the value that was passed in via arguement/parameter #... in int, double, etc... (I think)... */
	//print_r(func_get_args());  /* Get the value that was passed in via arguement/parameter #... in arrays (I think)... */

	$loop_count1 = func_num_args();
	$junk_array1 = func_get_args();
	$xyz = 0;

	for ($x = 0; $x < $loop_count1; $x++) {
		$array_count1 = count($junk_array1[$x]);

		if ($array_count1 != 0) {
			for ($y = 0; $y < $array_count1; $y++) {
				$new_array1[$xyz] = $junk_array1[$x][$y];
				$xyz++;
			}
		}
	}

	$new_array2 = array_unique($new_array1); /* Work a lot like DISTINCT() in SQL... */

	return $new_array2;
}

function pwb_register_shutdown_function($key, & $function) {
	if (!isset ($_SESSION['shutdown_functions'])) {
		$_SESSION['shutdown_functions'] = array ();
	}
	$_SESSION['shutdown_functions'][$key] = & $function;
}

function pwb_shutdown() {
	$fs = @ $_SESSION['shutdown_functions'];
	if (!is_array($fs))
		return;
	foreach (array_keys($fs) as $i) {
		$f = & $fs[$i];
		$f->call();
	}
}
register_shutdown_function('pwb_shutdown');
?>