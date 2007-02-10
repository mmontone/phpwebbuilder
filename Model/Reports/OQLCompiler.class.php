<?php

class OQLCompiler {
	function fromQuery($query, $env) {
			$oqlg =&PHPCC::createGrammar(
				'<oql(
				   identifier::="[a-zA-Z_][a-zA-Z_0-9]*".
				   condition::=subexpression=>"\(" <expression> "\)"|comparison=><value> "=|<=|>=|LIKE" <value>.
				   expression::=logical=><condition> operator->"AND|OR|and|or" <condition>|condition=><condition>.
				   oql::=class->[<identifier>]
				   		 fields->["\(" fields->{<identifier> "as" <identifier> ; ","} "\)"]
				   		 from->["from" from->{var-><identifier> ":" class-><identifier> ; ","}]
				   		 where->["where" expression-><expression>].
				   variable::={<identifier> ; "\."}.
				   value::=var=><variable>|
				   		value=>(number=>"[0-9]+"|
				   		str=>"\'[^\']\'"|
				   		phpvar=>"\$[a-zA-Z_][a-zA-Z_0-9]*"|
				   		bool=>"TRUE|FALSE|True|False|true|false").
				   )>'
				);
			$oqlg->addPointCuts(array (
					'condition' => new FunctionObject($this, 'parseCondition'),
					'expression' => new FunctionObject($this, 'parseExpression'),
					'oql' => new FunctionObject($this, 'parseOql'),
					'variable' => new FunctionObject($this, 'parseVariable'),
					'value' => new FunctionObject($this, 'parseValue'),
			));
			$config =& $oqlg->compile($query);
			return 'new Report('.$config.');';
		}
		function &parseOQL(&$query){
			if ($query['class']!==null){
				$ret = "'class'=>'".$query['class']."',";
			}
			if ($query['fields']['fields']!==null){
				$ret .= "'fields'=>array(";
				foreach($query['fields']['fields'] as $f){
					if ($f==',')continue;
					$ret .= "'".$f[0]."'=>'".$f[2]."',";
				}
				$ret .= "),";
			}
			if ($query['from']['from']!==null){
				$ret .= "'from'=>array(";
				foreach($query['from']['from'] as $f){
					if ($f==',')continue;
					$ret .= "'".$f['var'].'\'=>\''.$f['class']."',";
				}
				$ret .= "),";
			}
			if ($query['where']['expression']!==null){
				$ret .= "'exp'=>".$query['where']['expression'];
			}
			$ret = 'array('.$ret.')';
			return $ret;
		}
		function &parseCondition(&$cond){
			if ($cond['selector']=='comparison'){
				$ret = 'new Condition(array(\'exp1\'=>'.$cond['result'][0].
						',\'operation\'=>\''.$cond['result'][1].'\''.
						',\'exp2\'=>'.$cond['result'][2].'))';
				return $ret;
			} else {
				return $cond['result'];
			}

		}
		function &parseExpression(&$cond){
			if ($cond['selector']=='condition'){
				return $cond['result'];
			} else {
				if ($cond['result']['operator']=='AND'){
					$class= 'AndExp';
				} else if ($cond['result']['operator']=='OR') {
					$class= 'OrExp';
				}
				$ret = 'new '.$class.'(array('.$cond['result'][0].
						','.$cond['result'][1].'))';
				return $ret;
			}
		}
		function &parseValue(&$cond){
			if ($cond['selector']=='value'){
				$ve =  'new ValueExpression("'.$cond['result']['result'].'")';
				return $ve;
			} else {
				return $cond['result'];
			}
		}
		function &parseVariable(&$cond){
			$path = '';
			for($i=0;$i<count($cond)-1;$i+=2){
				$path .= $cond[$i]. '.';
			}
			$path = substr($path,0,-1);
			$ap =  'new AttrPathExpression(\''.$path.'\',\''.$cond[$i].'\')';
			return $ap;
		}
}

?>