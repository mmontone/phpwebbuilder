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
		$this->setObj($arr = array('object'=>$field->obj()));
		return $fc;
	}
	function select(){
		$sc =& new SelectCollectionComponent($this->field->collection);
		$sc->registerCallbacks(array('selected'=>callback($this,'setObj')));
		$this->fc->call($sc);
	}
	function setObj(&$params){
		$obj =& $params['object'];
		$v = $obj->indexValues();
		if ($v==""){
			$v = 'choose';
		}
		$this->fc->add_component(
			new ActionLink($this, 'select', $v, $n=null),'value'
		);
		$this->vh->setValue($obj->id->value);
	}
}

?>