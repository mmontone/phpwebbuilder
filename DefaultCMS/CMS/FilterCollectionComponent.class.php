<?php

require_once 'NavigationComponent.class.php';

class FilterCollectionComponent extends ObjectComponent {
	var $col;
	var $classN;
	function FilterCollectionComponent(&$col) {
		$this->col = & $col;
		$this->classN = $col->dataType;
		$c = $col->dataType;
		parent :: ObjectComponent(new $c);
	}
	function initialize() {
		$this->factory =& new SearchComponentFactory;
		parent::initialize();
		$this->addComponent(new ActionLink($this, 'filter','Search',$n=null));
	}
	function addField($name, &$field){
		parent::addField($name, &$field);
		$cs =& $this->col->conditions;
		$this->fields[$name]->comparator->setValue($cs[$name][0]);
		$this->fields[$name]->value->setValue($cs[$name][1]);
	}
	function filter(){
		$fs =& $this->fieldNames;
		$cs =& $this->col;
		$ks = array_keys($fs);
		foreach($ks as $k){
			$fc =& $this->fields[$k];
			if ($fc->comparator->getValue()!="" ||
				$fc->value->getValue()!=""){
				$cs->conditions[$k][0] = $fc->comparator->getValue();
				$cs->conditions[$k][1] = $fc->value->getValue();
			} else {
				unset($cs->conditions[$k]);
			}
		}
		$this->callback('done');
	}
}
?>