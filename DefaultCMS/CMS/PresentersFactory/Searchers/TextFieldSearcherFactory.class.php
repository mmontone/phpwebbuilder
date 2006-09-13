<?php

class TextFieldSearcherFactory extends SearcherFactory {
	function &createInstanceFor(&$field){
		return new TextFieldSearcher();
	}
}

class TextFieldSearcher extends DataFieldSearcher{
	function setSearchValue(&$conds, $fname){
		$this->comp = $conds[$fname][0];
		$s = substr(substr($conds[$fname][1], 0, -1), 1);
		$this->val = $s;
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