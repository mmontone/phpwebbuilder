<?php

class ObjectComponent extends Component {
	var $obj;
	var $classN;
	var $fieldNames;
	var $fieldComponents;
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
    	$fs =& new FormComponent($n=null);
    	foreach($this->fieldNames as $f2){
    		$this->addField($f2, $fields[$f2]);
       	}
    }
    function addField($name, &$field){
		$fc =& new FormComponent($n=null);
		$this->addComponent($fc, $name);
		$fc->addComponent(new Label($field->displayString), 'fieldName');
		$this->fieldComponents[$name] = &$this->factory->createFor($field);
		$fc->addComponent($this->fieldComponents[$name], 'value');
    }
}

?>