<?php

require_once 'EditComponentFactory.class.php';

class IndexFieldEditComponentFactory extends EditComponentFactory {
	var $fc;
	var $field;
	var $vh;
	function &componentForField(&$field){
		$vh =& new ValueHolder($n=null);
		$fc =& new FormComponent($vh);
		$this->fc =& $fc;
		$this->vh =& $vh;
		$this->field =& $field;
		if ($field->getValue()!=0){
			$this->setObj($arr = array('object'=>$field->obj()));
		} else {
			$this->setObj($arr = array());
		}
		return $fc;
	}
	function select(){
		$sc =& new SelectCollectionComponent($this->field->collection);
		$sc->registerCallbacks(array('selected'=>callback($this,'setObj')));
		$this->fc->call($sc);
	}
	function setObj(&$params){
		$obj =& $params['object'];
		if ($obj!=null) {
			$v = $obj->indexValues();
			if ($v==""){
				$v = 'choose';
			}
			$id = $obj->getIdOfClass($this->field->collection->dataType);
			$this->vh->setValue($id);
		} else {
			$v = 'choose';
		}
		$this->fc->addComponent(
			new ActionLink($this, 'select', $v, $n=null),'value'
		);
	}
}

?>