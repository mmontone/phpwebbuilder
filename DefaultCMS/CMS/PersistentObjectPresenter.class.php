<?php

class PersistentObjectPresenter extends Component {
	var $obj;
	var $classN;
	var $fieldNames;

    function PersistentObjectPresenter(&$object, $fields=null) {
		$this->obj =& $object;
		$this->classN =& getClass($object);
		if ($fields===null){
    		$this->fieldNames =& $this->obj->allFieldNames();
		} else {
			$ks = array_keys($fields);
			if (is_subclass_of($fields[$ks[0]],'DataField')){
				$this->fieldNames =& array_map(create_function('$field','return $field->colName;'),$fields);
			} else {
				$this->fieldNames =& $fields;
			}
		}
    	parent::Component();
    }

    function initialize(){
    	$obj =& $this->obj;

		$fields =& $obj->fieldsWithNames($this->fieldNames);
       	foreach(array_keys($fields) as $f2){
    		$this->addComponent($this->addField($fields[$f2]), $f2);
       	}
    }

    function &addField(&$field){
		$fc =& new FieldValueComponent;
		$fieldComponent = & $this->factory->createFor($field);
		$fc->addComponent($fieldComponent, 'value');
		$fc->addComponent(new Label($field->displayString), 'fieldName');
		return $fc;
    }
}

class FieldValueComponent extends Component{}
?>