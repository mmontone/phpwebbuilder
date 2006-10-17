<?php

class CollectionFilterer extends PersistentObjectPresenter {
	var $col;
	var $classN;
	function CollectionFilterer(&$col) {
		$this->col = & $col;
		$this->classN = $col->dataType;
		$c = $col->dataType;
		parent :: PersistentObjectPresenter(new $c);
	}
	function initialize() {
		$this->factory =& new SearcherFactory;
		parent::initialize();
		$this->addComponent(new ActionLink($this, 'filter','Search',$n=null));
	}
	function &addField(&$field){
		$fc =& parent::addField(&$field);
		$cs =& $this->col->getConditions();
		$fc->value->setSearchValue($cs, $field->colName);
		return $fc;
	}
	function filter(){
		$fs =& $this->fieldNames;
		$cs =& $this->col;
		$ks = array_keys($fs);
		foreach($ks as $k){
			$sc =& $this->$k;
			$sc->value->getSearchValue($cs, $k);
		}
		$this->callback('done');
	}
}
?>