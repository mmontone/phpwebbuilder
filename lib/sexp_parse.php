<?php

$character_dispatchers = array('"' => 'read_literal_string',
                              '(' => 'read_list');

function sexp_parse(&$chars) {
	global $character_dispatchers;
	$dispatching_character = array_shift($chars);
	$func = $character_dispatchers[$dispatching_character];

	if ($func != null) {
		return $func($chars);
	}
	else {
		array_unshift($chars, $dispatching_character);
		return read_symbol($chars);
	}
}

function read_list(&$chars) {
	$elems = array();
	$c = array_shift($chars);

	if ($c == null) {
		echo 'Unmatching parenthesis error';exit;
	}

	while ($c == ' ' or $c == "\n" or $c == "\t") {
		$c = array_shift($chars);
		if ($c == null) {
			echo 'Unmatching parenthesis error';exit;
		}
	}

	while ($c != ')') {
		array_unshift($chars, $c);
		array_push($elems,sexp_parse($chars));

		$c = array_shift($chars);

		if ($c == null) {
			echo 'Unmatching parenthesis error';exit;
		}

		while ($c == ' ' or $c == "\n" or $c == "\t") {
			$c = array_shift($chars);
			if ($c == null) {
				echo 'Unmatching parenthesis error';exit;
			}
		}
	}

	return $elems;
}

function read_symbol(&$chars) {
	$c = array_shift($chars);
	$sym = '';
	while ($c != ' ' and $c != ')' and $c != "\n" and $c != "\t") {
		$sym .=  $c;
		$c = array_shift($chars);

		if ($c == null) {
			echo 'Parenthesis matching error';exit;
		}
	}

	array_unshift($chars, $c);

	return $sym;
}

function read_literal_string(&$chars) {
	$c = array_shift($chars);
	$str = '';
	while ($c != '"') {
		$str .=  $c;
		$c = array_shift($chars);
		if ($c == null) {
			echo 'Unmatching parenthesis error';exit;
		}
	}

	$s =& new LiteralString;
	$s->str = $str;
	return $s;
}

class LiteralString {
	var $str;

	function printString() {
		return $this->str;
	}
}


?>
