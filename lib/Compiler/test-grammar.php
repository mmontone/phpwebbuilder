<?php

require_once 'PHPCC.class.php';
require_once dirname(dirname(dirname(__FILE__))).'/Core/FunctionObject.class.php';

error_reporting(E_ALL);
ini_set('memory_limit', '32M');
htmlshow(PHPCC::ccGrammar()->print_tree());
/* We first define the grammar*/

$g =& PHPCC::createGrammar(
					'<expression(
					   value::=	number=>/[0-9]+/ |
							str=>/\'[^\']+\'/ |
						   	bool=>/TRUE|FALSE/i.
					   expression::= tree=>exp1-><expression> operator->("+"|"-") exp2-><expression>|value=><term>.
					   term::= tree=>exp1-><term> operator->("*"|"\\") exp2-><term> | value=><value>.
					)>');
/* Then, the processing functions */

function evalValue($params){
	return (int)$params['result'];
}
function evalExpression($params){
	if ($params['selector']=='tree'){
		$elem = $params['result']['exp1'].$params['result']['operator']['result'].$params['result']['exp2'];
		var_dump($elem);
		return eval('return '.$elem.';');
	} else /* Just a simple value */{
		return $params['result'];
	}
}

/* Just re-printing the grammar, debugging */

//echo(nl2br(sp2nbsp(htmlentities(print_r($g)))));
echo(nl2br(sp2nbsp(htmlentities($g->print_tree()))));

/* Parsing a simple expression */

echo '<br/>parsing 2+3';
var_dump( $g->compile('2+3'));

/* We add the pointcuts for the non-terminals (if we don't specify one for a non-terminal, it just returns the default AST) */

$g->addPointCuts(array('expression'=>new FunctionObject($n=null,'evalExpression'),
					  'term'=>new FunctionObject($n=null,'evalExpression'),
					  'value'=>new FunctionObject($n=null,'evalValue')));

/* Now parsing AND processing */

foreach(array('2*5
* 9 		-3*8','2*5
* 9 		-3*','25a', '3++', "\n2\n++\n\n 3\n") as $input){
	echo '<br/>parsing '.$input;
	var_dump( $g->compile($input));
}
?>
