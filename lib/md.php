<?php

require_once 'spyc-0.2.3/spyc.php';

// Call with multiple dispatch

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
	$fname = _mdcall($function, $c, 0);

	if ($fname != null) {
		$params = array();
		for($i = 0; $i < $n; $i++) {
			$params[$i] = '$args[' . $i . ']';
		}

		//echo 'Dispatching to: ' . $fname . '(' . print_r($c, true) . ')<br/>';
		eval('$res =& ' . $fname . '(' . implode(',', $params) . ');');
		return $res;
	} else {
		print_backtrace('Dispatch failed: ' . $function . '(' . print_r($c, true) . ');');
	}
}

function _mdcall($function, $arg_types, $i) {
	$n = count($arg_types);
	if ($i == $n) {
		$fname = $function;
		for ($j = 0; $j < $n; $j++) {
			$fname .= '_' . strtoupper($arg_types[$j]);
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

// No se puede usar porque es ineficiente!!! :S
// No se como hacer multiple dispatch eficiente

function _mdcompcall($function, $count_layers, $arg_types, $i) {
	$n = count($arg_types);
	if ($i == $n) {
		$fname = $function . '_begctx';


		for ($j = 0; $j < $count_layers; $j++) {
			$fname .= '_' . strtoupper($arg_types[$j]);
		}
		$fname .= '_endctx';

		for ($j = $count_layers; $j < $n; $j++) {
			$fname .= '_' . strtoupper($arg_types[$j]);
		}

		trace('Checking for ' . $fname . '</br>');
		if (function_exists($fname)) {
			return $fname;
		}
		else {
			return null;
		}
	}
	else {
		$fname = _mdcompcall($function, $count_layers, $arg_types, $i + 1);
		$parent = get_parent_class($arg_types[$i]);
		while (($fname == null) and $parent) {
			$arg_types[$i] = $parent;
			$fname = _mdcompcall($function, $count_layers, $arg_types, $i + 1);
			$parent = get_parent_class($arg_types[$i]);
		}

		return $fname;
	}
}


function &mdcompcall($function, $args, $max_nesting=3) {
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
	$flayers = md_get_layers($comp, $max_nesting);
	$layers = $flayers;
	$fname = null;

	$msg = 'Trying to match:<br/>';
	$msg .= 'Context: ' . print_r($flayers,true) . '<br/>';
	$msg .= 'Function:' . $function . '(' . print_r($c, true) . ');<br/>';
	//echo $msg;

	while (!empty($layers) and $fname == null) {
		$f = $function . '_begctx_' . implode('_', $layers) . '_endctx';
		$fname = _mdcall($f, $c, 0);

		// _mdcompcall es mas flexible pero extremadamente ineficiente
		//$fname = _mdcompcall($function, count($layers), array_merge($layers, $c), 0);

		array_shift($layers);
	}

	if ($fname != null) {
		$params = array();
		for($i = 0; $i < $n; $i++) {
			$params[$i] = '$args[' . $i . ']';
		}

		//echo 'Dispatching to: ' . $fname . '(' . print_r($c, true) . ')<br/>';
		eval('$res =& ' . $fname . '(' . implode(',', $params) . ');');
		return $res;
	}
	else {
		$msg = 'Dispatch failed: <br/>';
		$msg .= 'Context: ' . print_r($flayers,true) . '<br/>';
		$msg .= 'Function:' . $function . '(' . print_r($c, true) . ');<br/>';
		print_backtrace($msg);
	}
}

function md_get_layers(&$comp, $max_nesting = 3) {
	$layers = array(strtoupper(get_class($comp)));
	$c =& $comp->getParent();

	$i = 0;
	while(($c !== null) and (getClass($c) != 'stdclass') and ($i < $max_nesting)) {
		array_push($layers, strtoupper(get_class($c)));

		$c =& $c->getParent();
		$i++;
	}
	$layers = array_reverse($layers);
	return $layers;
}

function load_md_functions($file) {
	$functions = Spyc::YAMLLoad($file);
	if (empty($functions)) {
		print_backtrace('There are no definitions in ' . $file);
	}
	else {
		foreach($functions as $f => $rules) {
			def_md_function($f, $rules);
		}
	}
}

function def_md_function($f, $rules) {
	foreach($rules as $rule_def) {
		if (is_array($rule_def['in'])) {
			$s = def_md_comprule_rule($f, $rule_def);
		}
		else {
			$s = def_md_rule_rule($f, $rule_def);
		}

		//echo 'Defining: ' . $s . '<br/><br/>';
		eval($s);
	}
}

function def_md_rule_rule($f, $ruledef) {
	$params = $ruledef['with'];
	$body = $ruledef['do'];
	$s = "function &$f";

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
