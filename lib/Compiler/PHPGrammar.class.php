<?php

class PHPGrammar {
	function PHPGrammar(){
'php_script ::= "<\?" interface_def* class_def+ "\?>".

interface_def ::= INTERFACE_NAME extends:INTERFACE_NAME* member* .

class_def ::= class_mod CLASS_NAME extends:CLASS_NAME?
   implements:INTERFACE_NAME* member* .
class_mod ::= "abstract"? "final"? .

member ::= method | attribute .

method ::= signature statement*? .
signature ::= method_mod is_ref:"&"? METHOD_NAME formal_parameter* .
method_mod ::= "public"? "protected"? "private"?
   "static"? "abstract"? "final"? .
formal_parameter ::= type is_ref:"&"? VARIABLE_NAME expr? .
type ::= "array"? CLASS_NAME? .

attribute ::= attr_mod VARIABLE_NAME expr? .
attr_mod ::= "public"? "protected"? "private"? "static"? "const"?  .

Statements

statement ::=
     if | while | do | for | foreach
   | switch | break | continue | return
   | static_declaration
   | unset | declare | try | throw | eval_expr .

if ::= expr iftrue:statement* iffalse:statement* .
while ::= expr statement* .
do ::= statement* expr .
for ::= init:expr? cond:expr? incr:expr? statement* .
foreach ::= expr key:variable? is_ref:"&"?
   val:variable statement* .

switch ::= expr switch_case* .
switch_case ::= expr? statement* .
break ::= expr? .
continue ::= expr? .
return ::= expr? .

static_declaration ::= VARIABLE_NAME expr? .
unset ::= variable .

declare ::= directive+ statement* .
directive ::= DIRECTIVE_NAME expr .

try ::= statement* catches:catch* .
catch ::= CLASS_NAME VARIABLE_NAME statement* .
throw ::= expr .

eval_expr ::= expr .

Expressions

expr ::=
     assignment | list_assignment | cast | unary_op | bin_op
   | conditional_expr | ignore_errors | constant | instanceof
   | variable | pre_op | post_op | array
   | method_invocation | new | clone
   | literal .

literal ::= INT | REAL | STRING | BOOL | NULL .

assignment ::= variable is_ref:"&"? expr .

list_assignment ::= list_elements expr .
list_elements ::= list_element?* .
list_element ::= variable | list_elements .

cast ::= CAST expr .
unary_op ::= OP expr .
bin_op ::= left:expr OP right:expr .

conditional_expr ::=
   cond:expr iftrue:expr iffalse:expr .
ignore_errors ::= expr .

constant ::= CLASS_NAME CONSTANT_NAME .

instanceof ::= expr class_name .

variable ::= target? variable_name
   array_indices:expr?* string_index:expr? .
variable_name ::= VARIABLE_NAME | reflection .
reflection ::= expr .

target ::= expr | CLASS_NAME .

pre_op ::= OP variable .
post_op ::= variable OP .

array ::= array_elem* .
array_elem ::= key:expr? is_ref:"&"? val:expr .

method_invocation ::= target method_name actual_parameter* .
method_name ::= METHOD_NAME | reflection .

actual_parameter ::= is_ref:"&"? expr .

new ::= class_name actual_parameter* .
class_name ::= CLASS_NAME | reflection .

clone ::= expr .

Additional Structure

node ::=
     php_script | class_mod | signature
   | method_mod | formal_parameter | type | attr_mod
   | static_var | directive | list_element | variable_name | target
   | array_elem | method_name | actual_parameter | class_name
   | commented_node | expr | identifier
   | formal_parameter* | directive* | array_elem* | actual_parameter*
   | INTERFACE_NAME* | list_element* | expr*
   .

commented_node ::=
     member | statement | interface_def | class_def | switch_case
	| catch interface_def* | class_def* | member* | statement*
	| switch_case* | catch*
   .

identifier ::=
     INTERFACE_NAME | CLASS_NAME | METHOD_NAME | VARIABLE_NAME
   | DIRECTIVE_NAME | CAST | OP | CONSTANT_NAME
   .';
	}
}
?>