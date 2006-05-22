<?php

class ObjectComponent extends Component {
	var $obj;
	var $classN;
	var $fieldNames;
	var $fields;
    function ObjectComponent(&$object, $fields=null) {
		$this->obj =& $object;
		$this->classN = get_class($object);
		if ($fields==null){
    		$this->fieldNames =& $this->obj->allFieldNames();
		} else {
			$this->fieldNames = $fields;
		}
    	parent::Component();
    }
    function initialize(){
    	$obj =& $this->obj;
    	$fields =& $obj->fieldsWithNames($this->fieldNames);
    	foreach($this->fieldNames as $f2){
    		$this->addField($f2, $fields[$f2]);
       	}
    }
    function addField($name, &$field){
		$fc =& new FormComponent($n=null);
		$this->addComponent($fc, $name);
		$fc->addComponent(new Text(new ValueHolder($name)), 'name');
		$this->fields[$name] = &$this->factory->createFor($field);
		$fc->addComponent($this->fields[$name], 'value');
    }
}

?>