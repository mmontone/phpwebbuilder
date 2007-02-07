<?php

class OQLCompiler {

	function & fromQuery($query, $env) {
		$variable = & new ListParser(new Identifier, new Symbol('\.'));
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
					'class'=> new Identifier,
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
				$self =& $this;
				$oqlg = & new Grammar(array (
				'root' => 'oql',
				'nt' => array (
					'condition' => array(&$condition, new FunctionObject($self, 'parseCondition')),
					'expression' => array(&$expression, new FunctionObject($self, 'parseExpression')),
					'oql' => array(&$oql, new FunctionObject($self, 'parseOql')),
					'variable' => array(&$variable, new FunctionObject($self, 'parseVariable')),
					'value' => array(&$value, new FunctionObject($self, 'parseValue')),
				)
			));
			$config = $oqlg->compile($query);
			return 'new Report('.$config.');';
		}
		function parseOQL($query){
			if ($query['class']!==null){
				$ret .= "'class'=>'".$query['class']."',";
			}
			if ($query['fields']['fields']!==null){
				$ret .= "'fields'=>array(";
				foreach($query['fields']['fields'] as $f){
					$ret .= "'".$f[0]."',";
				}
				$ret .= "),";
			}
			if ($query['from']['from']!==null){
				$ret .= "'vars'=>array(";
				foreach($query['from']['from'] as $f){

					$ret .= "'".$f[0]['var'].'\'=>\''.$f[0]['class']."',";
				}
				$ret .= "),";
			}
			if ($query['where']['expression']!==null){
				$ret .= "'exp'=>".$query['where']['expression'];
			}
			return 'array('.$ret.')';
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
			for($i=0;$i<count($cond)-1;$i++){
				$path .= $cond[$i][0]. '.';
			}
			$path = substr($path,0,-1);
			$ap =  'new AttrPathExpression(\''.$path.'\',\''.$cond[$i][0].'\')';
			return $ap;
		}
}

?>