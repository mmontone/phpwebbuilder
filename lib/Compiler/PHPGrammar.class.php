<?php

class PHPGrammar {
	function &Grammar(){
		return PHPCC::createGrammar(
'<php_script(
php_script ::= ("<?php"|"<?") (<class_def>|<function_def>|<statement>)* "\?>".

class_def ::= "class" <identifier> ["extends" <identifier>] "{" <member>* "}" .

member ::= <function_def> | <attribute> .

function_def ::= <signature> "{" <statement>* "}" .
signature ::= ["&"] <identifier> "(" {<formal_parameter>;","} ")".
formal_parameter ::= ["&"] <variableName> ["=" <value>] .

attribute ::= "var" <variableName> ["=" <value>]  ";".

statement ::=
     <if> | <while> | <do> | <for> | <foreach>
   | <switch> | "break" ";" | "continue" ";" | <return> | <expression> ";" | "{" <statement>* "}".

if ::= "if" "(" <expression> ")" <statement> ["else" <statement>].
while ::= "while" "(" <expression> ")" <statement> .
do ::= "do" <statement> "while" "(" <expression> ")"  .
for ::= "for" "(" <expression> ";" <expression> ";" <expression> ")" <statement>.
foreach ::= "foreach" "(" <expression> "as" [<expression>"=>"]<expression> ")" <statement>.

switch ::= "switch" <expression> "{" ("case" <value> ":" <statement>)+ "}".

return ::= "return" [<expression>] ";".

expression2 ::=
     <assignment> | <cast> | <unary_op> | <bin_op>
   | <conditional_expr> | <ignore_errors>
   | <variableName> | <pre_op> | <post_op> | <array>
   | <method_invocation> | <new>
   | <literal> .
'./*
expression:
*/'
expression ::= <function_call>.

literal ::= /[0-9]+/ | /[0-9]+\.[0-9]+/ | /"[^"]"/ | /\'[^\']\'/ | /true|false/i  | /null/i .
function_call ::= <identifier> "(" {<expression>; "," } ")".
array ::= "array" "(" {<value>; "," } ")".
assignment ::= <variableName> "=" ["&"] <expression> .
array_access::= <element>"["<expression>"]".
member_access::=<element>"->"(<variableName>|<identifier>|"{"<expression>"}").

cast ::= "(" /string|int/i ")" <expression> .
unary_op ::= "-" <expression> .
bin_op ::= <variableName> ("+"|"-"|"*"|"/") <expression> .

conditional_expr ::= <expression> "?"<expression>":"<expression>.
ignore_errors ::= "@" <expression>.

pre_op ::= ("++"|"--") <variableName>.
post_op ::= <variableName> ("++"|"--").

identifier::=/[a-z_][a-z_0-9]*/i.
variableName::="$" <identifier>.
)>');
	}
	function parse($php){
		if (!isset($GLOBALS['phpg'])){
			$GLOBALS['phpg'] =& PHPGrammar::Grammar();
			header('content-type: text/plain');
			echo $GLOBALS['phpg']->print_tree();
		}
		echo $php,"\n";
		print_r($GLOBALS['phpg']->compile($php));exit;
	}
}
?>