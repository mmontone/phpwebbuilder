<?php

require_once dirname(__FILE__) . '/sexp_parse.php';

$next_var = 0;
$out = '';

function sexp_parse_str($str) {
	 print_backtrace();
     $chars = str_split($str);
	 $res = sexp_parse($chars);
	 if (!empty($chars)) {
	 	echo 'Unmatching parenthesis error';exit;
	 }

	 return $res;
}

function freeVar() {
	global $next_var;

	return '$__fv_' . (string)$next_var++;
}


function write($str) {
	global $out;
	//$out .= "$str<br/>";
	$out .= "$str\n";
}

$defined_vars = array();

function define_var($report, $var, $type) {
	global $defined_vars;
	write($report . "->defineVar('$var', '$type');");
	$defined_vars[$var] = $type;
}

function get_var_type($var) {
	global $defined_vars;
	return $defined_vars[$var];
}

/* TODO: use dynVar for report */
$report = array();

function defreport() {
	global $report;
    $var = freeVar();
    array_push($report, $var);
    return $var;
}

function get_report() {
	global $report;
	return $report[count($report) - 1];
}

function undefreport() {
	global $report;
	array_pop($report);
}


// The macro

function query($text) {
	global $out;
	$out = '';
	$list = sexp_parse_str($text);
	$select = $list[0];


	if (is_array($select)) { echo 'Implement'; exit;}

	$report = defreport();
	$col = freeVar();
	write("if (class_exists('$select')) {");
		write("$col =& new PersistentCollection('$select');");
		write("$report =& new CompositeReport($col);");
	write("}
	else {");
		write("$report =& new Report;");
		write($report . "->select('$select');");
	write("}");

	$from = $list[4];

	foreach ($from as $var_type) {
		$var = $var_type[0];
		$type = $var_type[1];
		define_var($report, $var, $type);
	}

	$where = $list[6];
	$cmd = $where[0];
	array_shift($where);
	$where_exp = dispatch_where_cmd($cmd, $where);

	write($report . "->setSelectExp($where_exp);");
	undefreport();

	$to = $list[2];
	write("$to =& $report;");

	return $out;
}

$where_cmd_funcs = array('and' => 'write_and',
						'or' => 'write_or',
                         '=' => 'write_eq',
                         'like' => 'write_like',
                         'exists' => 'write_exists',
                         'contains' => 'write_contains',
                         'forall' => 'write_forall',
                         'not' => 'write_not');

function dispatch_where_cmd($cmd, $body) {
	global $where_cmd_funcs;

	$func = $where_cmd_funcs[$cmd];

	if (is_null($func)) {
		echo 'Invalid command: ' . $cmd;
		exit;
	}

	return $func($body);
}

function write_and($exps) {
	$and = freeVar();

	write("$and =& new AndExp;");

	foreach ($exps as $exp) {
		$cmd = $exp[0];
		array_shift($exp);
		write($and ."->addExpression(" . dispatch_where_cmd($cmd, $exp) . ');');
	}

	return $and;
}

function write_or($exps) {
	$or = freeVar();

	write("$or =& new OrExpression;");

	foreach ($exps as $exp) {
		$cmd = $exp[0];
		array_shift($exp);
		write($or ."->addExpression(" . dispatch_where_cmd($cmd, $exp) . ');');
	}

	return $or;
}

function write_not($exp) {
	$cmd = $exp[0];
	array_shift($exp);
	return "new NotExpression(" .  dispatch_where_cmd($cmd, $exp) . ');';
}

function write_eq($exps) {
	$eq = freeVar();

	write("$eq =& new EqualCondition;");

	$i = 1;
	foreach ($exps as $exp) {
		write($eq ."->exp" . $i++ . " =& " . read_reference($exp) . ';');
	}

	return $eq;
}

function write_bin_op($op, $exps) {
	$e = freeVar();

	write("$e =& new Condition;");

	write($e . "->operation = '$op';");

	$i = 1;
	foreach ($exps as $exp) {
		write($e ."->exp" . $i++ . " =& " . read_reference($exp) . ';');
	}

	return $e;
}

function write_like($exps) {
	return write_bin_op('LIKE', $exps);
}

function read_reference($exp) {
	if ($exp[0] == 'exp') {
		return 'new ValueExpression(' . $exp[1]->printString() . ')';
	}
	else if ($exp[0] == 'obj') {
		$obj = freeVar();
		write("$obj =& " . $exp[1]->printString() . ';');
		return 'new ValueExpression(' . $obj .  ' ->getId())';
	}
	else {
		$attr = array_pop($exp);

		$exp = evaluate_path($exp);
		$path = implode('.', $exp);
		return "new AttrPathExpression(\"$path\",\"$attr\")";
	}
}

function evaluate_path($path) {
	$new = array();
	foreach ($path as $elem) {
		if (is_array($elem) and $elem[0] == 'exp') {
			$e = $elem[1]->printString();
		}
		else {
			$e = $elem;
		}
		array_push($new, $e);
	}
	return $new;
}

/*
function write_exists($exp) {
	$var = $exp[0];
	$collection_ref = $exp[1];
	$body =  $exp[2];

	$path = implode('.', $collection_ref);
	$report = get_report();
	write($report  . "->accessCollection('$path','$var');");

	$select = defreport();
	write("$select =& new Report;");
	write($select . "->select(*);");

	$cmd = $body[0];
	array_shift($body);
	$where_exp = dispatch_where_cmd($cmd, $body);
	write($select . "->setSelectExp($where_exp);");

	undefreport();

	return "new ExistsExpression($select)";
}
*/

function write_exists($exp) {
	$var = $exp[0];
	$collection_ref = $exp[1];
	$body =  $exp[2];


	$col_field = array_pop($collection_ref);
	$path = implode('.', $collection_ref);

	$cmd = $body[0];
	array_shift($body);

	$report = get_report();
	$select = defreport();
	write("$select =& new Report;");
	write($select . "->select('*');");

	// Declare all free variables
	write($select ."->vars = " . $report . "->vars;");

	$where_exp = dispatch_where_cmd($cmd, $body);

	undefreport();

	$and = freeVar();
	write("$and =& new AndExp;");
	write($and ."->addExpression(new CollectionPathExpression('$path', '$col_field', '$var'));");
	write($and ."->addExpression($where_exp);");

	write($select . "->setSelectExp($and);");

	return "new ExistsExpression($select)";
}


function write_forall($exp) {
	$var = $exp[0];
	$collection_ref = $exp[1];
	$body =  $exp[2];

	$path = implode('.', $collection_ref);
	$report = get_report();
	write($report  . "->accessCollection('$path','$var');");

	$cmd = $body[0];
	array_shift($body);
	return dispatch_where_cmd($cmd, $body);
}

function write_contains($exp) {
	$var  = $exp[0];
	$collection_ref = $exp[1];

	$path = implode('.', $collection_ref);
	$report = get_report();
	$s = freeVar();
	write("$s =& " . $report  . "->getCollectionSelect('$path');");

	return "new InExpression('$var', $s)";
}


/*
$query = '(LiteralString
               to $query
               from ((usergroup CozzuolUserGroup))
			   where (and (and (= (usergroup user) (obj "$user"))
				                   (= (usergroup group area) (obj "$document->getArea()")))
				              (exists docperm (usergroup group permissions)
				                   (and (= (docperm (exp "$action")) (exp "1"))
				                        (= (docperm docType) (obj "$document->getDocType()"))))))';

class Tema {

}
$aquery = '(Tema ' .
		   'to $query ' .
		   'from ((tema Tema))' .
		   	'where (and (= (tema reunion publica) (exp "true"))' .
		   	'           (like (tema titulo) (exp "$titulo"))' .
		   	'           (contains trackedperson (tema trackedpersons))))';
		   	;

$x = select($query);
echo $x;*/
?>
