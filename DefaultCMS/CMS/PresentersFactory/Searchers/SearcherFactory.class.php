<?php

class SearcherFactory extends FieldPresenterFactory {}

class DataFieldSearcherFactory extends SearcherFactory {
	function &createInstanceFor(&$field){
		return new DataFieldSearcher();
	}
}

class DataFieldSearcher extends Component{
	var $comp, $val;
	function initialize(){
		$this->addComponent(new Input(new ValueHolder($this->comp)), 'comparator');
		$this->addComponent(new Input(new ValueHolder($this->val)), 'value');
	}
	function setSearchValue(&$conds, $fname){
		$this->comp = $conds[$fname][0];
		$this->val = $conds[$fname][1];
	}
	function getSearchValue(&$col, $fname){
		if ($this->comparator->getValue()!="" ||
			$this->value->getValue()!=""){
			$col->conditions[$fname][0] = $this->comparator->getValue();
			$col->conditions[$fname][1] = $this->value->getValue();
		} else {
			unset($col->conditions[$fname]);
		}
	}
}

?>