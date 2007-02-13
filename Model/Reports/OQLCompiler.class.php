<?php

class OQLCompiler {
	function fromQuery($query, $env) {
			$oqlg =&PHPCC::createGrammar(
				'<oql(
				   identifier::=/[a-z_][a-z_0-9]*/i.
				   phpvar::=/\$[a-z_][a-z_0-9]*/i.
				   condition::=subexpression=>"(" <expression> ")"|comparison=><value> /=|<=|>=|\>|\<|LIKE|is/i <value>.
				   expression::=logical=><condition> operator->/AND|OR/i <expression>|
				   			condition=><condition>|
				   			not=>/NOT|not/ <expression>|
							exists=>/EXISTS/i "(" <oql> ")"|
							in=><variable> /IN/i "(" <oql> ")".
				   oql::=phpvar=><phpvar>|query=>class->[[<identifier> ":"]<identifier>]
					   		 fields->["(" fields->{<identifier> "as" <identifier> ; ","} ")"]
					   		 from->["from" from->{var-><identifier> ":" class-><identifier> ; ","}]
					   		 where->["where" expression-><expression>]
						.
				   variable::={<identifier> ; "."}.
				   value::=var=><variable>|
				   		value=>(
							number=>/[0-9]+/|
					   		str=>/\'[^\']\'/|
					   		phpvar=><phpvar>|
					   		bool=>/TRUE|FALSE|True|False|true|false/).
				   )>'
				);
			$oqlg->addPointCuts(array (
					'condition' => new FunctionObject($this, 'parseCondition'),
					'expression' => new FunctionObject($this, 'parseExpression'),
					'oql' => new FunctionObject($this, 'parseOql'),
					//'variable' => new FunctionObject($this, 'parseVariable'),
					//'value' => new FunctionObject($this, 'parseValue'),
			));
			$config =& $oqlg->compile($query);
			return $config;
		}
		function &parseOQL(&$query){
			if ($query['selector']=='phpvar'){
				return $query['result'];
			} else {
				$query =& $query['result'];
			}
			if ($query['class']!==null){
				$ret = "'class'=>'".$query['class'][1]."',";
				if ($query['class'][0]){
					$query['from']['from'][]=array('var'=>$query['class'][0][0],'class'=>$query['class'][1]);
				}
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
			$ret = 'new Report(array('.$ret.'))';
			return $ret;
		}
		function &parseCondition(&$cond){
			if ($cond['selector']=='comparison'){
				if ($cond['result'][1]=='is'){
					$cond['result'][1] = '=';
					$cond['result'][0]=$this->parseObject($cond['result'][0]);
					$cond['result'][2]=$this->parseObject($cond['result'][2]);
				} else {
					$cond['result'][0]=$this->parseValue($cond['result'][0]);
					$cond['result'][2]=$this->parseValue($cond['result'][2]);
				}
				$ret = 'new Condition(array(\'exp1\'=>'.$cond['result'][0].
						',\'operation\'=>\''.$cond['result'][1].'\''.
						',\'exp2\'=>'.$cond['result'][2].'))';
				return $ret;
			} else {
				return $cond['result'];
			}

		}
		function &parseExpression(&$cond){
			switch($cond['selector']){
				case 'not':
					return 'new NotExp("'.$cond['result'][1].'")';
				case 'condition':
					return $cond['result'];
				case 'exists':
					return 'new ExistsExpression('.$cond['result'][2].')';
				case 'in':
					return 'new InExpression("'.$cond['result'][0].'",'.$cond['result'][3].')';
				default:
					if (strcasecmp($cond['result']['operator'],'AND')==0){
						$class= 'AndExp';
					} else if (strcasecmp($cond['result']['operator'],'OR')==0) {
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
				return $this->parseVariable($cond['result']);
			}
		}
		function &parseObject(&$cond){
			if ($cond['selector']=='value'){
				$ve =  'new ObjectExpression('.$cond['result']['result'].')';
				return $ve;
			} else {
				return 'new ObjectPathExpression(\''.implode($cond['result']).'\')';
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