<?php
class CompositeReport extends Report {
	var $report;

	function CompositeReport(& $report) {
		#@typecheck $report:Report@#
		$this->report = & $report;
		parent :: Report();
		$this->setEventsBubbling();
	}
	function & fromQuery($query) {
		preg_match('/^((\w)+)?' . '[\s\t\n]*' .
		'(\((.*)\))?' . '[\s\t\n]*' .
		'(from (.+))?' . '[\s\t\n]*' .
		'(where ((.|\n)*))?' . '$/i', $query, $matches);
		echo '<br/>';
		$class = $matches[1];
		$c = & new CompositeReport(new PersistentCollection($class));
		$subfields = $matches[4];
		if ($subfields != '') {
			foreach (explode(',', $subfields) as $sf) {
				$fd = explode('as', $sf);
				$fields[trim($fd[0])] = trim($fd[1]);
			}
			$c->fields = & $fields;
		}

		$from = $matches[6];
		foreach (explode(',', $from) as $fs) {
			$fd = explode(':', $fs);
			$c->defineVar(trim($fd[0]), trim($fd[1]));
		}
		$where = $matches[8];
		$matches = preg_split('/(\W)/i', str_replace("\n", ' ', $where), -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
		$last = & new AndExp();
		$word = '';
		for ($i = 0;$i<count($matches);$i++) {
			$match = $matches [$i];
			switch ($match) {
				case ' ' :break;
				case 'or' :case 'OR' : case '(' : print_backtrace_and_exit("'$match' not supported yet");
				case 'AND' :
				case 'and' :
					$lastExpression->exp2=& new ValueExpression($word);
					$word = '';
					break;
				case '=': case '<=':case '>=':case '<':case '>':case 'LIKE':case 'like':
					$path= substr($word, 0, strrpos($word,'.'));
					$attr= substr($word, strrpos($word,'.')+1);
					$last->addExpression($lastExpression =& new Condition(array (
							'exp1' => new AttrPathExpression($path, $attr),
							'operation'=>$match,
							'exp2' => new Expression
						)));
					$word = '';
					break;
				case '$':
				case '.' :
				default :
					$word .= $match;
					//echo 'not handled:'.$word;
			}
		}
		$lastExpression->exp2=& new ValueExpression($word);
		$last->evaluateIn($c);
		$c->select_exp->addExpression($last);
		return $c;
	}

	function printString() {
		return $this->primPrintString('Report: ' . $this->report->printString());
	}

	function setEventsBubbling() {
		$this->report->addInterestIn('changed', new FunctionObject($this, 'changed'));
	}

	/*
	function bubbleUpEvent(&$collection, $event) {
	    $this->triggerEvent('changed', $this);
	    return;
	}*/

	function & getTables() {
		return array_union_values($this->tables, $this->report->getTables());
	}

	function & getConditions() {
		return array_merge($this->conditions, $this->report->getConditions());
	}

	function & getSelectExp() {
		$e = & new AndExp;
		$e->addExpression($this->select_exp);
		$se =& $this->report->getSelectExp();
		$e->addExpression($se);

		return $e;
	}

	function & getGroup() {
		return array_merge($this->group, $this->report->getGroup());
	}

	function & getOrder() {
		return array_merge($this->order, $this->report->getOrder());
	}

	function & getLimit() {
		if ($this->limit == 0) {
			return $this->report->getLimit();
		} else {
			return $this->limit;
		}
	}

	function & getOffset() {
		if ($this->offset == 0) {
			return $this->report->getOffset();
		} else {
			return $this->offset;
		}
	}

	function & getFields() {
		return array_merge($this->fields, $this->report->getFields());
	}

	function getDataType() {
		return $this->report->getDataType();
	}

	function setDataType($dataType) {
		$this->report->setDataType();
	}
}

function array_union_values() {
	//echo func_num_args();  /* Get the total # of arguements (parameter) that was passed to this function... */
	//print_r(func_get_arg());  /* Get the value that was passed in via arguement/parameter #... in int, double, etc... (I think)... */
	//print_r(func_get_args());  /* Get the value that was passed in via arguement/parameter #... in arrays (I think)... */

	$loop_count1 = func_num_args();
	$junk_array1 = func_get_args();
	$xyz = 0;

	for ($x = 0; $x < $loop_count1; $x++) {
		$array_count1 = count($junk_array1[$x]);

		if ($array_count1 != 0) {
			for ($y = 0; $y < $array_count1; $y++) {
				$new_array1[$xyz] = $junk_array1[$x][$y];
				$xyz++;
			}
		}
	}

	$new_array2 = array_unique($new_array1); /* Work a lot like DISTINCT() in SQL... */

	return $new_array2;
}
?>