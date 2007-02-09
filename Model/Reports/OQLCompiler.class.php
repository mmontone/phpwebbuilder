<?php

class OQLCompiler {
	function fromQuery($query, $env) {
		/*$variable = & new ListParser(new Identifier, new Symbol('\.'));
		$value = & new AltParser($a0=array (
			'var'=>new SubParser('variable'
			), 'value'=>new AltParser(array('number'=>new EregSymbol('[0-9]+'),
											'str'=>new EregSymbol('\'[^\']\''),
											'phpvar'=>new EregSymbol('\$[a-zA-Z_][a-zA-Z_0-9]*'),
											'bool'=>new Symbols(array('TRUE','FALSE', 'True', 'False','true','false'))
			))));

		$condition = & new AltParser(array (
			'subexpression'=>new SeqParser(array (new Symbol('\('),
				new SubParser('expression'), new Symbol('\)'))
				),
			'comparison'=>new SeqParser(array (new SubParser('value'
				), new Symbols(array (
			'=',
			'<=',
			'>=',
			'LIKE'
		)), new SubParser('value')))));

				$expression = & new AltParser(array (
				'logical'=>new SeqParser(array (
					new SubParser('condition'),
					'operator'=>new Symbols(array ('AND','OR','and', 'or')),
					new SubParser('condition')
					)),
				'condition'=>new SubParser('condition'),
				));

			$oql = & new SeqParser(array (
					'class'=>new MaybeParser( new Identifier),
					'fields'=> new MaybeParser(
						new SeqParser(array (
							new Symbol('\('
							),
							'fields'=>new ListParser(
								new SeqParser(array(
									//new AltParser(array('count'=>new Symbol('count\(\*\)'),
										//				'field'=>
														new Identifier,
										//)),
									new Symbol('as'),
									new Identifier,
								)),
								new Symbol(','
								)),
							new Symbol('\)')
							))
						),
					'from'=> new MaybeParser(
						new SeqParser(array (
							new Symbol('from'),
							'from'=>new ListParser(
								new SeqParser(array (
									'var'=>new Identifier,
									new Symbol(':'),
									'class'=>new Identifier
									)),
								new Symbol(',')
								)
							))
						),
					'where'=> new MaybeParser(
						new SeqParser(array (
							new Symbol('where'),
							'expression'=>new SubParser('expression')
							))
						),
				));
				$oqlg = & new Grammar(array (
				'root' => 'oql',
				'nt' => array (
					'condition' => &$condition,
					'expression' => &$expression,
					'oql' => &$oql,
					'variable' => &$variable,
					'value' => &$value,
				)));*/

			//header('Content-type: text/plain');
			$oqlg =&PHPCC::createGrammar(
				'<oql(
				   condition::=subexpression=>"\(",<expression>,"\)"|comparison=><value>,"=|<=|>=|LIKE",<value>.
				   expression::=logical=><condition>,operator->"AND|OR|and|or",<condition>|condition=><condition>.
				   oql::=class->["[a-zA-Z_][a-zA-Z_0-9]*"],
				   		 fields->["\(",fields->{"[a-zA-Z_][a-zA-Z_0-9]*","as","[a-zA-Z_][a-zA-Z_0-9]*";","},"\)"],
				   		 from->["from",from->{var->"[a-zA-Z_][a-zA-Z_0-9]*",":",class->"[a-zA-Z_][a-zA-Z_0-9]*";","}],
				   		 where->["where",expression-><expression>].
				   variable::={"[a-zA-Z_][a-zA-Z_0-9]*";"\."}.
				   value::=var=><variable>|value=>(number=>"[0-9]+"|str=>"\'[^\']\'"|phpvar=>"\$[a-zA-Z_][a-zA-Z_0-9]*"|bool=>"TRUE|FALSE|True|False|true|false").
				   )>'
				);
			$oqlg->addPointCuts(array (
					'condition' => new FObject($this, 'parseCondition'),
					'expression' => new FObject($this, 'parseExpression'),
					'oql' => new FObject($this, 'parseOql'),
					'variable' => new FObject($this, 'parseVariable'),
					'value' => new FObject($this, 'parseValue'),
			));
			$config =& $oqlg->compile($query);
			return 'new Report('.$config.');';
		}
		function &parseOQL(&$query){
			$query =& $query['result'];
			if ($query['class']!==null){
				$ret = "'class'=>'".$query['class']['result'][0]."',";
			}
			if ($query['fields']['result']['fields']!==null){
				$ret .= "'fields'=>array(";
				foreach($query['fields']['result']['fields'] as $f){
					if ($f[0]==',')continue;
					$ret .= "'".$f['result'][0]."'=>'".$f['result'][2]."',";
				}
				$ret .= "),";
			}
			if ($query['from']['result']['from']!==null){
				$ret .= "'from'=>array(";
				foreach($query['from']['result']['from'] as $f){
					if ($f['result'][0]==',')continue;
					$ret .= "'".$f['result']['var'].'\'=>\''.$f['result']['class']."',";
				}
				$ret .= "),";
			}
			if ($query['where']['result']['expression']!==null){
				$ret .= "'exp'=>".$query['where']['result']['expression'];
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
				$ve =  'new ValueExpression("'.$cond['result'][0]['result'][0].'")';
				return $ve;
			} else {
				return $cond['result'][0];
			}
		}
		function &parseVariable(&$cond){
			$cond = $cond['result'][0];
			$path = '';
			for($i=0;$i<count($cond)-1;$i+=2){
				$path .= $cond[$i]['result'][0]. '.';
			}
			$path = substr($path,0,-1);
			$ap =  'new AttrPathExpression(\''.$path.'\',\''.$cond[$i]['result'][0].'\')';
			return $ap;
		}
}

?>