<?php

class SubReport extends CompositeReport {
    var $parent = null;
    function &getVar($id) {
		$var =& parent::getVar($id);
        if ($var!=null) {
            return $var;
        }
        else {
       		return $this->parent->getVar($id);
        }
    }
	function printString(){
        $vars = array();
        foreach ($this->vars as $var) {
        	$vars[] = $var->id . ':' . $var->class;
        }
        return $this->primPrintString('(' . $this->getDataType() . $this->parent->printString().') vars: ' . implode(',', $vars));
	}
}
?>