<?php

class PHPGrammar {
	function PHPGrammar(){
		$oqlg =&PHPCC::createGrammar('php_script ::= "<\?" (<class_def>|<function_def>|<sentence>)* "\?>".

class_def ::= "class" <identifier> ["extends" <identifier>] "{" <member>* "}" .

member ::= <method> | <attribute> .

method ::= <signature> "{" statement* "}" .
signature ::= ["&"] <identifier> "(" {<formal_parameter>;","} ")".
formal_parameter ::= ["&"] <variableName> ["=" <value>] .

attribute ::= "var" <variableName> ["=" <value>] .

statement ::=
     <if> | <while> | <do> | <for> | <foreach>
   | <switch> | "break" ";" | "continue" ";" | <return> | <expression> ";" | "{" <statement>* "}".

if ::= "if" "(" <expression> ")" statement ["else" <statement>].
while ::= "while" "(" <expression> ")" <statement> .
for ::= "for" "(" <expression> ";" <expression> ";" <expression> ")" <statement>
foreach ::= "foreach" "(" <expression> "as" [<expression>"=>"]<expression> ")" <statement>

switch ::= "switch" <expression> "{" ("case" <value> ":" <statement>)+ "}".

return ::= "return" [<expression>] ";".

expression ::=
     <assignment> | <cast> | <unary_op> | <bin_op>
   | <conditional_expr> | <ignore_errors> | <constant>
   | <variable> | <pre_op> | <post_op> | <array>
   | <method_invocation> | <new>
   | <literal> .

literal ::= INT | REAL | STRING | BOOL | NULL .

assignment ::= <variableName> "=" ["&"] <expression> .

cast ::= CAST expr .
unary_op ::= "-" <expression> .
bin_op ::= <variable> ("+"|"-"|"*"|"/") <expression> .

conditional_expr ::=
   <expression> "?"<expression>":"<expression>.
ignore_errors ::= "@" <expression>.

pre_op ::= ("++"|"--") <variable>.
post_op ::= <variable> ("++"|"--").');
	}
}
?>