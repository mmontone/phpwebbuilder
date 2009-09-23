<?php

// Multiple dispatch library

require_once 'spyc-0.2.3/spyc.php';

/* MD with macros:
#@defmdf getListElement [ TrackedPersonsNavigator <- TrackedObjectsList ] (&$guest : TrackedPerson, &$state : InGuestState)
     {
           return new InGuestElement($guest);
     }
 @#*/

function md_echo ($code) {
    return optionalCompile('md_echo', $code);
}

function defmdf($text) {
	preg_match('/(&?[[:alpha:]]*)[\s\t]*(?:\[(.*)\])?[\s\t]*\((.*?)\)[\s\t]*\{(.*)\}/s', $text, $matches);
	//print_r($matches);
	$name = $matches[1];
	$context = $matches[2];
	$params = $matches[3];
	$body = $matches[4];
	//echo 'Name: ' . $name;
	//echo 'Context: ' . $context;
	//echo 'Params: ' . $params;
	//echo 'Body:' . $body;

	$rules = array();
	if ($context != '') {
		$cs = explode('<-',$context);
		foreach (array_keys($cs) as $i) {
			$cs[$i] = trim($cs[$i]);
		}
		$rules['in'] = $cs;
	}

	$ps = explode(',', $params);
	$pss = array();
	foreach($ps as $p) {
		$pp = explode(':', $p);
		$arg = trim($pp[0]);
		$type = trim(str_replace('<','__tp_',str_replace('>','_tp__',$pp[1])));
		$pss[$arg] = $type;
	}
	$rules['with'] = $pss;
	$rules['do'] = $body;
	//print_r($rules);
	//echo '<br /><br />';
	return compile_md_function($name, array($rules));
}

// Configuration
$mdc_dir = basedir . '/mdc';
$md_dir = basedir . '/md';

if (defined('md_dir')) {
	$md_dir = constant('md_dir');
}

if (defined('mdc_dir')) {
	$md_dir = constant('mdc_dir');
}

if (constant('md')=='compiled') {
	load_compiled_md_files($mdc_dir);
}
else {

	load_md_files($md_dir);
}

function load_md_files($dir) {
	foreach(getfilesrec(lambda('$file','$v=substr($file, -3)==".md";return $v;', $a=array()), $dir) as $f){
        load_md_file($f);
 	}
}


function load_compiled_md_files($dir) {
	foreach(getfilesrec(lambda('$file','$v=substr($file, -4)==\'.php\';return $v;'), $dir) as $f){
        //echo "Requiring $f <br />";
        require_once $f;
 	}
}

function compile_md_src($src) {
	$s = '';
	foreach($src as $f => $rules) {
		$s .= compile_md_function($f, $rules);
		$s .= "\n";
	}

	return $s;
}

function load_md_file($file) {
	$compiled_src = compile_md_src(Spyc::YAMLLoad($file));
	eval($compiled_src);
}

function &mdcall($function, $args) {
	$c = array();
	$n = count($args);
	for ($i = 0; $i < $n ; $i++) {
		$argi =& $args[$i];
		if (is_object($argi)) {
			$c[$i] = get_class($argi);
		}
		else {
			$c[$i] = gettype($argi);
		}
	}

	//echo 'Multiple dispatch: ' . $function . '(' . print_r($c, true) . ')<br/>';
	$fname = findMdFunction($function, $args);
	//$fname = _mdcall($function, $c, 0);

	if ($fname != null) {
		$params = array();
		for($i = 0; $i < $n; $i++) {
			$params[$i] = '$args[' . $i . ']';
		}

		//echo 'Dispatching to: ' . $fname . '(' . print_r($c, true) . ')<br/>';
		$res = null;
		eval('$res =& ' . $fname . '(' . implode(',', $params) . ');');
		return $res;
	} else {
		print_backtrace('error</b>:<br/>Dispatch failed: ' . $function . '(' . print_r($c, true) . ');');
	}
}

function findMdFunction($function, $params){
	return findMdContextFunction($function, $null, $params);
}


function findMdContextFunction($function, &$comp, $params){
	$fs = get_defined_functions();
	$funs = array();
	$params = array_reverse($params);
	$size = strlen($function);
	$function = strtolower($function);
	$layers = md_get_layers($comp);
	//echo '<br/> searching'.$function;
	//foreach($layers as $p){echo ' '.getClass($p);}
	//echo ' params: ';
	//foreach($params as $p){echo ' '.getClass($p);};
	$better = array();
	foreach ($fs['user'] as $fun0){
		$fun = strtolower(str_replace('__tp_','<',(str_replace('_tp__','>',$fun0))));
		if (substr($fun, 0, $size)==$function){
			$types = array_reverse(explode('_',substr($fun, $size+1))); //The "+1" is for the first '_'
			//echo '<br/>checking '.$fun; print_r($types);
			//Hasta count($params) son parametros, luego son contexto.
			//
			$endctx=count($params);
			$matches = true;
			foreach(array_keys($types) as $i){
				//echo '<br/>typing '.$types[$i];
				if ($i<$endctx){
					//echo ',search in params';
					$target=& $params[$i];
				} else {
					if ($i==$endctx || $i==count($types)-1) continue;
					//echo ',search in context, layer '.($i-$endctx-1);
					$target=& $layers[$i-$endctx-1];
				}
				//echo ' comparing type '.getClass($target).' to '.$types[$i];
				if (!(
					hasType($target,$types[$i])
					&&
						(@$better[$i]==null || is_subclass($types[$i],$better[$i]))
					  )) {
					$matches = false;
					//echo '<br/>failed matching '.getClass($target).' to '.$types[$i]. ', better is '.print_r($better,TRUE);
					break;
				}
			}
			if ($matches) {$better = $types;$betterfun = $fun0;$funs []=$fun0;}
		}
	}
	//var_dump($funs);
	//var_dump($betterfun);
	return $betterfun;
}

function &mdcompcall($function, $args) {
	$c = array();
	$n = count($args);
	for ($i = 1; $i < $n ; $i++) {
		$argi =& $args[$i];
		if (is_object($argi)) {
			$c[$i-1] = get_class($argi);
		}
		else {
			$c[$i-1] = gettype($argi);
		}
	}

	$comp =& $args[0];
	$fname = null;
	/*
	#@md_echo
    $msg = 'Trying to match:<br/>';
    $msg .= 'Context: ' . print_r($flayers,true) . '<br/>';
    $msg .= 'Function:' . $function . '(' . print_r($c, true) . ');<br/>';
    echo $msg;//@#
    */
    $params = $args;
	array_shift($params);
	$fname = findMdContextFunction($function, $comp, $params);
	if ($fname != null) {
		$params = array();
		for($i = 0; $i < $n; $i++) {
			$params[$i] = '$args[' . $i . ']';
		}
		/*
		#@md_echo
        echo 'Dispatching to: ' . $fname . '(' . print_r($c, true) . ')<br/>';//@#
		*/
		$res = null;
		eval('$res =& ' . $fname . '(' . implode(',', $params) . ');');
		return $res;
	}
	else {
		$msg = 'error</b>:<br/>Dispatch failed: <br/>';
		//$msg .= 'Context: ' . print_r($flayers,true) . '<br/>';
		$msg .= 'Function:' . $function . '(' . print_r($c, true) . ');<br/>';
		print_backtrace_and_exit($msg);
	}
}

function md_get_layers(&$comp) {
	$c =& $comp;
	$layers=array();
	$i = 0;
	while(($c !== null) and (getClass($c) != 'stdclass')) {
		$layers[$i]=&$c;
		$c =& $c->getParent();
		$i++;
	}
	return $layers;
}

function compile_md_function($f, $rules) {
	$s = '';

	foreach($rules as $rule_def) {
		if (is_array(@$rule_def['in'])) {
			$s .= def_md_comprule_rule($f, $rule_def);
		}
		else {
			$s .= def_md_rule_rule($f, $rule_def);
		}
		$s .= "\n";

		//echo 'Defining: ' . $s . '<br/><br/>';
	}

	return $s;
}

function def_md_rule_rule($f, $ruledef) {
	$params = $ruledef['with'];
	$body = $ruledef['do'];
	$s = "function $f";

	if (!is_array($params)) $params = array();

	foreach($params as $param) {
		$s .= '_' . strtoupper($param);
	}

	$s .= '(' . implode(',', array_keys($params)) . ') {' . $body . '}';
	return $s;
}

function def_md_comprule_rule($f, $ruledef) {
	$context = $ruledef['in'];
	$params = $ruledef['with'];
	$body = $ruledef['do'];
	$s = "function $f";
	$s .= '_begctx';
	foreach($context as $c) {
		$s .= '_' . strtoupper($c);
	}
	$s .= '_endctx';

	if (!is_array($params)) $params = array();

	foreach($params as $param) {
		$s .= '_' . strtoupper($param);
	}

	$ps = array_merge(array('&$_context'), array_keys($params));
	$s .= '(' . implode(',', $ps) . ') {' . $body . '}';
	return $s;
}


?>
