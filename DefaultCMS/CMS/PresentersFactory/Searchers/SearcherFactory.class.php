<?php

require_once dirname(__FILE__).'/../FieldPresenterFactory.class.php';

class SearcherFactory extends FieldPresenterFactory {}

class DataFieldSearcherFactory extends SearcherFactory {
	function &componentForField(&$field){
		return new DataFieldSearcher();
	}
}

class DataFieldSearcher extends Component{
	function initialize(){
		$this->addComponent(new Input(new ValueHolder($c0="")), 'comparator');
		$this->addComponent(new Input(new ValueHolder($c1="")), 'value');
	}
	function setSearchValue(&$conds, $fname){
		$this->comparator->setValue($conds[$fname][0]);
		$this->value->setValue($conds[$fname][1]);
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