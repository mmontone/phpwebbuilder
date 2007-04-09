<?php

class PHPGrammar {
	function &Grammar(){
		return PHPCC::createGrammar(
'<php_script(
php_script ::= ("<?php"|"<?") (<require>|<class_def>|<function_def>|<statement>)* "?>".

require::= ("require"|"require_once") <expression> ";".

class_def ::= "class" <identifier> ["extends" <identifier>] "{" <member>* "}" .

member ::= <function_def> | <attribute> .

function_def ::= "function" <signature> "{" <statement>* "}" .
signature ::= ["&"] <identifier> "(" {<formal_parameter>;","] ")".
formal_parameter ::= ["&"] <variableName> ["=" <value>] .

attribute ::= "var" <variableName> ["=" <literal>]  ";".

statement ::=
     <if> | <while> | <do> | <for> | <foreach>
   | <switch> | "break" ";" | "continue" ";" | <return> | <expression> ";" | "{" <statement>* "}" |
	<echo>.

echo::="echo" {<expression> ; ","} ";".

if ::= "if" "(" <expression> ")" <statement> ["else" <statement>].
while ::= "while" "(" <expression> ")" <statement> .
do ::= "do" <statement> "while" "(" <expression> ")"  .
for ::= "for" "(" <expression> ";" <expression> ";" <expression> ")" <statement>.
foreach ::= "foreach" "(" <expression> "as" [<expression>"=>"]<expression> ")" <statement>.

switch ::= "switch" <expression> "{" ("case" <value> ":" <statement>)+ "}".

return ::= "return" [<expression>] ";".

expression ::=
     <assignment> | <cast> | <unary_op> | <bin_op>
   | <conditional_expr> | <ignore_errors>
   | <variableName> | <pre_op> | <post_op>
   | <member_access> |<array_access> | <new>
   | <literal> |<function_call> |"("<expression>")" | <identifier> | <class_method>.

literal ::= /[0-9]+/ | /[0-9]+\.[0-9]+/ | /"(\\\\"|[^"])*"/ | /\'(\\\\\'|[^\'])*\'/ | /true|false/i  | /null/i |<array>.
function_call ::= <identifier> "(" {<expression>; "," ] ")".
array ::= "array" "(" {[<expression> "=>"]<expression>; "," ] ")".
assignment ::= <expression> ("="|"=&"|"+=") <expression> .
array_access::= <expression> "[" [<expression>] "]".
member_access::=<expression> "->" (<variableName>|<identifier>|"{"<expression>"}"|<function_call>).
new::= "new" ( <identifier> | <variableName> ) ["(" {<expression>; "," ] ")"].
class_method ::= <identifier>"::"<function_call>.

cast ::= "(" /string|int/i ")" <expression> .
unary_op ::= ("-"|"!"|"not") <expression> .
bin_op ::= <expression>
				("+"|"-"|"*"|"/"|"."
				|"=="|"==="|"!="|"!=="
				|">"|"<"|"<="|">="
				|"||"|"&&"|"or"|"and") <expression> .

conditional_expr ::= <expression> "?"<expression>":"<expression>.
ignore_errors ::= "@" <expression>.

pre_op ::= ("++"|"--") <expression>.
post_op ::= <expression> ("++"|"--").

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