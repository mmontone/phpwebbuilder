<?php
class CompositeReport extends Report {
	var $report;

	function CompositeReport(& $report) {
		#@typecheck $report:Report@#
		$this->report = & $report;
		parent :: Report();
		$this->setEventsBubbling();
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
		$this->select_exp->evaluateIn($this);
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