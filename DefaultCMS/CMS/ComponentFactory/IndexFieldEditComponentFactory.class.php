<?php

require_once 'EditComponentFactory.class.php';

class IndexFieldEditComponentFactory extends EditComponentFactory {
	var $fc;
	var $field;
	function &componentForField(&$field){
		$vh =& new ValueHolder($field->getValue());
		$fc =& new FormComponent($vh);
		$this->fc =& $fc;
		$this->field =& $field;
		$fc->add_component(new Input($vh),'value');
		$fc->add_component(new ActionLink($this, 'newComponent', 'new', $n), 'new');
		return $fc;
	}
	function newComponent(){
		$d = $this->field->collection->dataType;
		$obj =& new $d;
		$this->fc->call(new EditObjectComponent($obj));
	}
}

?>