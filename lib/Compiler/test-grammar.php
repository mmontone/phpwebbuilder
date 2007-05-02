<?php

define('pwbdir',dirname(dirname(dirname(__FILE__))));
define('basedir',dirname(dirname(dirname(__FILE__))));
define('app_class','Test-grammar');
$_SESSION=array('shutdown_functions'=>array());
require_once dirname(dirname(dirname(__FILE__))).'/lib/basiclib.php';
require_once 'PHPCC.class.php';
require_once dirname(dirname(dirname(__FILE__))).'/Core/FunctionObject.class.php';

highlight_string('<?php '.substr(file_get_contents(__FILE__), 981));

?>
<script>
function hideshowchild(elem){
	if (elem.nextSibling.style.visibility=='hidden'){
		elem.nextSibling.style.visibility = 'inherit';
		elem.nextSibling.style.width = 'auto';
		elem.nextSibling.style.height = 'auto';
	} else {
		elem.nextSibling.style.visibility = 'hidden';
		elem.nextSibling.style.width = '0';
		elem.nextSibling.style.height = '0';
	}
}

</script>

<?

error_reporting(E_ALL);
ini_set('memory_limit', '32M');
htmlshow(PHPCC::ccGrammar()->print_tree());
/* We first define the grammar*/
ob_start();
$g =& PHPCC::createGrammar(
		'<expression(
		   value::=	number=>/[0-9]+/ |
				str=>/\'[^\']+\'/ |
			   	bool=>/TRUE|FALSE/i.
		   expression::= tree=>exp1-><expression> operator->("+"|"-") exp2-><expression>|value=><term>.
		   term::= tree=>exp1-><term> operator->("*"|"\\") exp2-><term> | value=><value>.
		)>');
ob_end_clean();
echo '<br/>';
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
htmlshow($g->print_tree());

/* Parsing a simple expression */

echo '<br/>parsing 2+3';
var_dump( $g->compile('2+3'));

/* We add the pointcuts for the non-terminals (if we don't specify one for a non-terminal, it just returns the default AST) */

$g->addPointCuts(array('expression'=>new FunctionObject($n=null,'evalExpression'),
					  'term'=>new FunctionObject($n=null,'evalExpression'),
					  'value'=>new FunctionObject($n=null,'evalValue')));

/* Now parsing AND processing */

foreach(array('2*5*9','25a') as $input){
	echo '<br/>parsing '.$input;
	var_dump( $g->compile($input));
}
?>
