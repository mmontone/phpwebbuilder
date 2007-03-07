<?php

class OQLCompiler {
	var $error;
	function fromQuery($query) {
			global $grammars;
				if (!isset($grammars['oqlGrammar'])){
				$grammars['oqlGrammar'] =&PHPCC::createGrammar(
					'<oql(
					   identifier::=/[a-z_][a-z_0-9]*/i.
					   phpvar::=/\$[a-z_][a-z_0-9]*/i.
					   condition::=subexpression=>"(" <expression> ")"|comparison=><value> /=|\<\>|<=|>=|\>|\<|LIKE|IS/i <value>.
					   valueorfunction::=<identifier> ["(" {<identifier>; ","} ")"].
					   expression::=
					   			not=>/NOT/i <expression>|
								exists=>/EXISTS/i "(" <oql> ")"|
								in=><variable> /IN/i "(" <oql> ")"||
								logical=><condition> [operator->/AND|OR/i <expression>].
					   oql::=class->[name=>[<identifier> ":"]<identifier>|phpvar=><phpvar>]
						   		 fields->["(" fields->{<valueorfunction> "as" <identifier> ; ","} ")"]
						   		 from->["from" from->{var-><identifier> ":" class-><identifier> ; ","}]
						   		 where->["where" expression-><expression>]
							.
					   variable::={<identifier> ; "."}.
					   value::=var=><variable>|
					   		value=>(
								number=>/[0-9]+/|
						   		str=>/\'[^\']+\'/|
						   		phpvar=><phpvar>|
						   		bool=>/TRUE|FALSE/i).
					   )>'
					);
				}
			$grammars['oqlGrammar']->setPointCuts(array (
					'condition' => new FunctionObject($this, 'parseCondition'),
					'expression' => new FunctionObject($this, 'parseExpression'),
					'oql' => new FunctionObject($this, 'parseOql'),
					'valueorfunction' => new FunctionObject($this, 'parsevalueorfunction'),

					//'variable' => new FunctionObject($this, 'parseVariable'),
					//'value' => new FunctionObject($this, 'parseValue'),
			));
			$config =& $grammars['oqlGrammar']->compile($query);
			if ($config==null) {
				$this->error = $grammars['oqlGrammar']->error;
			}
			return $config;
		}
		function toSQL($query){
			$repstr = $this->fromQuery($query);
			eval('$rep =& '.$repstr.';');
			$rep->evaluateAll();
			return 'new Report(array("sqls"=>"'.$repstr.'")';
		}
		function &parseOQL(&$query){
			if (@$query['class']['selector']=='phpvar'){
					$ret = "'subq'=>".$query['class']['result'].",";
				} else {
					$class=array();
					if (@$query['class']['result'][1]!=null) $class[]="'class'=>'".@$query['class']['result'][1]."'";
					if (@$query['class']['result'][0][0]!=null) $class[]="'target'=>'".@$query['class']['result'][0][0]."'";
					$ret = "'subq'=>new Report(array(".implode(',',$class).")),";
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
			$ret = 'CompositeReport::fromArray(array('.$ret.'))';
			return $ret;
		}
		function &parsevalueorfunction($arr){
			if (($arr[1])!==null){
				$val = $arr[0].$arr[1][0].implode('',$arr[1][1]).$arr[1][2];
			} else {
				$val = $arr[0];
			}
			return $val;
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
				return $cond['result'][1];
			}

		}
		function &parseExpression(&$cond){
			switch($cond['selector']){
				case 'not':
					$v = 'new NotExp('.$cond['result'][1].')';
					return $v;
				case 'exists':
					$v =  'new ExistsExpression('.$cond['result'][2].')';
					return $v;
				case 'in':
					$v =  'new InExpression("'.$cond['result'][0].'",'.$cond['result'][3].')';
					return $v;
				default:
					if ($cond['result'][1]==null){
						return $cond['result'][0];
					}
					if (strcasecmp($cond['result'][1]['operator'],'AND')==0){
						$class= 'AndExp';
					} else if (strcasecmp($cond['result'][1]['operator'],'OR')==0) {
						$class= 'OrExp';
					}
					$ret = 'new '.$class.'(array('.$cond['result'][0].
							','.$cond['result'][1][0].'))';
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
				$ob = 'new ObjectPathExpression(\''.implode($cond['result']).'\')';
				return $ob;
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