<?php

require_once 'CollectionNavigator.class.php';

class FilterCollectionComponent extends PersistentObjectPresenter {
	var $col;
	var $classN;
	function FilterCollectionComponent(&$col) {
		$this->col = & $col;
		$this->classN = $col->dataType;
		$c = $col->dataType;
		parent :: PersistentObjectPresenter(new $c);
	}
	function initialize() {
		$this->factory =& new SearchComponentFactory;
		parent::initialize();
		$this->addComponent(new ActionLink($this, 'filter','Search',$n=null));
	}
	function addField($name, &$field){
		parent::addField($name, &$field);
		$cs =& $this->col->conditions;
		$this->fieldComponents[$name]->setSearchValue($cs, $name);
	}
	function filter(){
		$fs =& $this->fieldNames;
		$cs =& $this->col;
		$ks = array_keys($fs);
		foreach($ks as $k){
			$sc =& $this->fieldComponents[$k];
			$sc->getSearchValue($cs, $k);
		}
		$this->callback('done');
	}
}
?>