<?php

require_once dirname(__FILE__).'/../FieldComponentFactory.class.php';

class TextFieldSearchComponentFactory extends SearchComponentFactory {
	function &componentForField(&$field){
		return new TextFieldSearchComponent();
	}
}

class TextFieldSearchComponent extends DataFieldSearchComponent{
	function setSearchValue(&$conds, $fname){
		$this->comparator->setValue($conds[$fname][0]);
		$s = substr(substr($conds[$fname][1], 0, -1), 1);
		$this->value->setValue($s);
	}
	function getSearchValue(&$col, $fname){
		if ($this->comparator->getValue()!="" ||
			$this->value->getValue()!=""){
			$col->conditions[$fname][0] = $this->comparator->getValue();
			$col->conditions[$fname][1] = '\''.$this->value->getValue().'\'';
		} else {
			unset($col->conditions[$fname]);
		}
	}
}

?>