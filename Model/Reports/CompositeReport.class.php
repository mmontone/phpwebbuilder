<?php
class CompositeReport extends Report {
	var $report;
    var $parent;

	function CompositeReport(& $report) {
		#@typecheck $report:Report@#
		parent :: Report();

        $this->report = & $report;
        $this->parent =& $report;
		$this->setEventsBubbling();
	}

    function &getTargetVar() {
        $v =& $this->report->getTargetVar();

        if ($v == null) {
    	    return parent::getTargetVar();
        }
        else {
        	return $v;
        }
    }

    function &getVar($id){
    	$v =& parent::getVar($id);

    	if ($v==null) {
    		return $this->report->getVar($id);
    	} else {
    		return $v;
    	}
    }

    function &fromArray($params){
		$cr =& new CompositeReport($params['subq']);
		$cr->setConfigArray($params);
		return $cr;
	}
	function printString() {
		$vars = array();
        foreach ($this->vars as $var) {
            $vars[] = $var->id . ':' . $var->class;
        }

        return $this->primPrintString('Report: ' . $this->report->printString() . ' Vars: ' . implode(',', $vars));
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
		$arr= array_union_values($this->tables, $this->report->getTables());
        return $arr;
	}

	function & getSelectExp() {
		$this->select_exp->evaluateIn($this);
        $e = & new AndExp;
		$e->addExpression($this->select_exp);
		$this->report->evaluated=false;
		$this->report->select_exp->evaluateIn($this);
        $se =& $this->report->select_exp;

		$e->addExpression($se);

		return $e;
	}

    function & getGroup() {
		$arr = array_merge($this->group, $this->report->getGroup());
		return $arr;
	}

	function & getOrder() {
		$arr = array_merge($this->order, $this->report->getOrder());
		return $arr;
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
		$arr = array_merge($this->fields, $this->report->getFields());
		return $arr;
	}

	function getDataType() {
		if (is_strict_subclass($this->dataType,'PersistentObject')){
			print_backtrace('composite datatype '.$this->dataType);
			return $this->dataType;
		} else {
			return $this->report->getDataType();
		}
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