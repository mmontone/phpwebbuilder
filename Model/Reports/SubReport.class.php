<?php

class SubReport extends CompositeReport {
    var $parent = null;
    var $collection = null;

       function &fromArray($params){

        if (!$params['subq']) {
           $s =& new SubReport(new Report());
           //print_backtrace('setting collection in' . $s->debugPrintString());
           $s->collection =& $params['collection'];
        }
        else {
        	$s =& new SubReport($params['subq']);
        }
        $s->setConfigArray($params);
        return $s;

    }
    function addEvalExpression(&$exp){
		$this->select_exp->addExpression($exp);
    }
    function &getTargetVar() {
        if (($this->target_var===null) and ($this->collection!==null)) {
            //print_backtrace('setting collection in' . $this->debugPrintString());
            $this->collection->parent =& $this;
            $this->collection->setTargetVar($this);
            $this->setPathCondition($this->collection);
            //$this->report->setTargetVar($this->collection->var, $this->getDataType());
        }
        //print_backtrace('Returning target var: ' . print_r($this->target_var,true) . ' in ' . $this->printString());
        if ($this->collection!==null){
            return $this->target_var;
        } else {
        	return parent::getTargetVar();
        }
    }
    function getDataType() {
    	if ($this->collection!==null){
            return $this->dataType;
        } else{
        	return parent::getDataType();
        }
    }
    function &getVar($id) {
		$var =& parent::getVar($id);
        if ($var!=null) {
            return $var;
        }
        else {
       		return $this->parent->getVar($id);
        }
    }
	/*
    function printString(){
        $vars = array();
        foreach ($this->vars as $var) {
        	$vars[] = $var->id . ':' . $var->class;
        }
        return $this->primPrintString('(' . $this->getDataType() . $this->parent->printString().') vars: ' . implode(',', $vars));
	}*/

    function debugPrintString() {
        return $this->primPrintString();
    }
}
?>