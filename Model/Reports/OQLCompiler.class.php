<?php

class OQLCompiler {
	var $error;
	function fromQuery($query) {
			$oqlg =&PHPCC::createGrammar(
				'<oql(
				   identifier::=/[a-z_][a-z_0-9]*/i.
				   phpvar::=/\$[a-z_][a-z_0-9]*/i.
				   condition::=subexpression=>"(" <expression> ")"|comparison=><value> /=|\<\>|<=|>=|\>|\<|LIKE|IS/i <value>.
				   valueorfunction::=<identifier> ["(" {<identifier>; ","} ")"].
				   expression::=logical=><condition> operator->/AND|OR/i <expression>|
				   			condition=><condition>|
				   			not=>/NOT/i <expression>|
							exists=>/EXISTS/i "(" <oql> ")"|
							in=><variable> /IN/i "(" <oql> ")".
				   oql::=class->(name=>[[<identifier> ":"]<identifier>]|phpvar=><phpvar>)
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
			$oqlg->addPointCuts(array (
					'condition' => new FunctionObject($this, 'parseCondition'),
					'expression' => new FunctionObject($this, 'parseExpression'),
					'oql' => new FunctionObject($this, 'parseOql'),
					'valueorfunction' => new FunctionObject($this, 'parsevalueorfunction'),

					//'variable' => new FunctionObject($this, 'parseVariable'),
					//'value' => new FunctionObject($this, 'parseValue'),
			));
			$config =& $oqlg->compile($query);
			if ($config==null) {
				$this->error = $oqlg->error;
			}
			return $config;
		}
		function &parseOQL(&$query){
			if ($query['class']!==null){
				if ($query['class']['selector']=='name'){
					$class=array();
					if ($query['class']['result'][1]!=null) $class[]="'class'=>'".$query['class']['result'][1]."'";
					if ($query['class']['result'][0][0]!=null) $class[]="'target'=>'".$query['class']['result'][0][0]."'";
					$ret = "'subq'=>new Report(array(".implode(',',$class).")),";
				} else {
					$ret = "'subq'=>".$query['class']['result'].",";
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
					$v = 'new NotExp("'.$cond['result'][1].'")';
					return $v;
				case 'condition':
					return $cond['result'];
				case 'exists':
					$v =  'new ExistsExpression('.$cond['result'][2].')';
					return $v;
				case 'in':
					$v =  'new InExpression("'.$cond['result'][0].'",'.$cond['result'][3].')';
					return $v;
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