<?php
class OQLCompiler {
	var $error;
	function fromQuery($query) {
		global $grammars;
		if (!isset ($grammars['oqlGrammar'])) {
			$grammars['oqlGrammar'] = & PHPCC :: createGrammar('<oql(
									   identifier::=/[a-z_][a-z_0-9]*/i.
									   phpvar::=/\$[a-z_][a-z_0-9]*/i.
									   condition::=subexpression=>"(" <expression> ")"|comparison=><value> /=|\<\>|<=|>=|\>|\<|LIKE|IS/i <value>.
									   valueorfunction::=<identifier> ["(" {(<valueorfunction>|| <value>); ","} ")"].
									   expression::=
									   			not=>/NOT/i <expression>|
												exists=>/EXISTS/i "(" <oql> ")"|
												in=><variable> /IN/i "(" <oql> ")"||
												logical=><condition> [operator->/AND|OR/i <expression>].
									   oql::=class->[name=>[<identifier> ":"]<identifier>|phpvar=><phpvar>|path=>{<identifier> ; "."} "as" <identifier>]
										   		 fields->["(" fields->{<valueorfunction> "as" <identifier> ; ","} ")"]
										   		 from->["from" from->{var-><identifier> ":" class-><identifier> ; ","}]
										   		 where->["where" expression-><expression>]
												 order->[/order by/i {<variable> /desc|asc/i;","}]
												 limit->[/limit/i <number>]
											.
									   variable::={<identifier> ; "."}.
									   plainsql::=/\[[^\]]+\]/.
									   number::=/[0-9]+/.
									   value::=
									   		value=>(
												number=><number>|
										   		str=>/\'[^\']+\'/|
										   		phpvar=><phpvar>|
										   		bool=>/TRUE|FALSE/i|
												plainsql=><plainsql>||
												aggregate=>"("<oql>")")||
											var=><variable>.
									   )>');
		}
		$grammars['oqlGrammar']->setPointCuts(array (
			'condition' => new FunctionObject($this,
			'parseCondition'
		), 'expression' => new FunctionObject($this, 'parseExpression'), 'oql' => new FunctionObject($this, 'parseOql'), 'valueorfunction' => new FunctionObject($this, 'parsevalueorfunction'), 'plainsql' => new FunctionObject($this, 'parseplainsql'),

		//'variable' => new FunctionObject($this, 'parseVariable'),
		//'value' => new FunctionObject($this, 'parseValue'),
		));
		$config = 'Composite'.substr($grammars['oqlGrammar']->compile($query),3);
		$this->error = $grammars['oqlGrammar']->getError();
		$this->res = $grammars['oqlGrammar']->res;
		return $config;
	}
	function toSQL($query) {
		$repstr = $this->fromQuery($query);
		eval ('$rep =& ' . $repstr . ';');
		$rep->evaluateAll();
		return 'new Report(array("sqls"=>"' . $repstr . '")';
	}
	function & parseOQL(& $query) {
		switch ($query['class']['selector']) {
			case 'path' :
				$path = $query['class']['result'][0];
				$real_path = array ();
				for ($i = 0; $i < count($path); $i += 2) {
					$real_path[] = $path[$i];
				}
                $col_field = array_pop($real_path);
                $var = $query['class']['result'][2];
				$target = 'new CollectionPathExpression(\'' . implode('.', $real_path) . '\', \'' . $col_field  . '\',\'' . $var . '\')';
				//$ret = "'subq'=> SubReport::fromArray(array('collection' => $target)),";
                $ret = "'collection' => $target,";
                break;
			case 'phpvar' :
				$ret = "'subq'=>" . $query['class']['result'] . ",";
				break;
			default :
				$class = array ();
				if (@ $query['class']['result'][1] != null)
					$class[] = "'class'=>'" . @ $query['class']['result'][1] . "'";
				if (@ $query['class']['result'][0][0] != null)
					$class[] = "'target'=>'" . @ $query['class']['result'][0][0] . "'";
				$ret = "'subq'=>new Report(array(" . implode(',', $class) . ")),";
		}

		if ($query['fields']['fields'] !== null) {
			$ret .= "'fields'=>array(";
			foreach ($query['fields']['fields'] as $f) {
				if ($f == ',')
					continue;
				$ret .= "'" . $f[0] . "'=>'" . $f[2] . "',";
			}
			$ret .= "),";
		}
		if ($query['from']['from'] !== null) {
			$ret .= "'from'=>array(";
			foreach ($query['from']['from'] as $f) {
				if ($f == ',')
					continue;
				$ret .= "'" . $f['var'] . '\'=>\'' . $f['class'] . "',";
			}
			$ret .= "),";
		}

		if ($query['where']['expression'] !== null) {
			$ret .= "'exp'=>" . $query['where']['expression'] . ',';
		}
		if ($query['order'] !== null) {
			$o='';
			foreach($query['order'][1] as $ord){
				$o .= '\''.$ord[0][0].'\'=>\''.$ord[1].'\',';
			}
			$ret .= "'order'=>array(" .$o . '),';
		}
		if ($query['limit'] !== null) {
			$ret .= "'limit'=>" . $query['limit'][1] . ',';
		}
		$ret = 'SubReport::fromArray(array(' . $ret . '))';

		return $ret;
	}
	function & parsevalueorfunction($arr) {
		if (($arr[1]) !== null) {
			$str = '';
			foreach ($arr[1][1] as $v) {
				if ($v['selector'] == 0) {
					$str .= $v['result'];
				} else {
					$str .= $v['result']['result']['result'];
				}
			}
			$val = $arr[0] . '(' . $str . ')';
		} else {
			$val = $arr[0];
		}
		return $val;
	}
	function & parseplainsql($arr) {
		$val = substr($arr, 1, count($arr) - 2);
		return $val;
	}
	function & parseCondition(& $cond) {
		if ($cond['selector'] == 'comparison') {
			if ($cond['result'][1] == 'is') {
				$cond['result'][1] = '=';
				$cond['result'][0] = $this->parseObject($cond['result'][0]);
				$cond['result'][2] = $this->parseObject($cond['result'][2]);
			} else {
				$cond['result'][0] = $this->parseValue($cond['result'][0]);
				$cond['result'][2] = $this->parseValue($cond['result'][2]);
			}
			$ret = 'new Condition(array(\'exp1\'=>' . $cond['result'][0] .
			',\'operation\'=>\'' . $cond['result'][1] . '\'' .
			',\'exp2\'=>' . $cond['result'][2] . '))';
			return $ret;
		} else {
			return $cond['result'][1];
		}

	}
	function & parseExpression(& $cond) {
		switch ($cond['selector']) {
			case 'not' :
				$v = 'new NotExp(' . $cond['result'][1] . ')';
				return $v;
			case 'exists' :
				$v = 'new ExistsExpression(' . $cond['result'][2] . ')';
				return $v;
			case 'in' :
				$v = 'new InExpression("' . $cond['result'][0] . '",' . $cond['result'][3] . ')';
				return $v;
			default :
				if ($cond['result'][1] == null) {
					return $cond['result'][0];
				}
				if (strcasecmp($cond['result'][1]['operator'], 'AND') == 0) {
					$class = 'AndExp';
				} else
					if (strcasecmp($cond['result'][1]['operator'], 'OR') == 0) {
						$class = 'OrExp';
					}
				$ret = 'new ' . $class . '(array(' . $cond['result'][0] .
				',' . $cond['result'][1][0] . '))';
				return $ret;
		}
	}
	function & parseValue(& $cond) {
		if ($cond['selector'] == 'value') {
			if ($cond['result']['selector'] == 'aggregate') {
				$ve = 'new AggregateExpression(' . $cond['result']['result'][1] . ')';
				return $ve;
			} else {
				$ve = 'new ValueExpression("' . $cond['result']['result'] . '")';
				return $ve;
			}
		} else {
			return $this->parseVariable($cond['result']);
		}
	}
	function & parseObject(& $cond) {
		if ($cond['selector'] == 'value') {
			$ve = 'new ObjectExpression(' . $cond['result']['result'] . ')';
			return $ve;
		} else {
			$ob = 'new ObjectPathExpression(\'' . implode($cond['result']) . '\')';
			return $ob;
		}
	}
	function & parseVariable(& $cond) {
		$path = '';
		for ($i = 0; $i < count($cond) - 1; $i += 2) {
			$path .= $cond[$i] . '.';
		}
		$path = substr($path, 0, -1);
		$ap = 'new AttrPathExpression(\'' . $path . '\',\'' . $cond[$i] . '\')';
		return $ap;
	}
}
?>