<?php
class CompositeReport extends Report {
	var $report;
  	var $e;

	function CompositeReport(& $report) {
		#@typecheck $report:Report@#
		parent :: Report();
        $this->report = & $report;
        $this->report->parent =& $this;
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
    		$n = null;
            $this->report->parent =& $n;
            $v =&  $this->report->getVar($id);
            $this->report->parent =& $this;
            return $v;
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
        if (!$this->e) {
    		$this->select_exp->evaluateIn($this);
            $this->e = & new AndExp;
      		$this->e->addExpression($this->select_exp);
            $se =& $this->report->getSelectExp();
      		$this->e->addExpression($se);
        }

       	return $this->e;
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
		return $this->report->getDataType();
    }
}

?>