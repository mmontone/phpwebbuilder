<?php

require_once 'FieldComponentFactory.class.php';

class SearchComponentFactory extends FieldComponentFactory {}

class DataFieldSearchComponentFactory extends SearchComponentFactory {
	function &componentForField(&$field){
		$fc =& new FormComponent($n=null);
		$fc->addComponent(new Input(new ValueHolder($c0="")), 'comparator');
		$fc->addComponent(new Input(new ValueHolder($c1="")), 'value');
		return $fc;
	}
}

?>