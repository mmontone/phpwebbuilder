<?php

class PersistentObjectPresenter extends Component {
	var $obj;
	var $classN;
	var $fieldNames;
	var $fieldComponents;
    function PersistentObjectPresenter(&$object, $fields=null) {
		$this->obj =& $object;
		$this->classN = getClass($object);
		if ($fields===null){
    		$this->fieldNames =& $this->obj->allFieldNames();
		} else {
			$ks = array_keys($fields);
			if (is_subclass_of($fields[$ks[0]],'DataField')){
				$this->fieldNames = array_map(create_function('$field','return $field->colName;'),$fields);
			} else {
				$this->fieldNames = $fields;
			}
		}
    	parent::Component();
    }

    function initialize(){
    	$obj =& $this->obj;
    	$fields =& $obj->fieldsWithNames($this->fieldNames);
       	foreach(array_keys($fields) as $f2){
    		$this->addField($f2, $fields[$f2]);
       	}
    }

    function addField($name, &$field){
		$fc =& new FieldValueComponent;
		$fc->addComponent(new Label($field->displayString), 'fieldName');
		$this->fieldComponents[$name] = &$this->factory->createFor($field);
		$fc->addComponent($this->fieldComponents[$name], 'value');
		$this->addComponent($fc, $name);
    }
}

class FieldValueComponent extends Component{}
?>