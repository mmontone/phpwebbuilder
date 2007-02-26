<?php

class PersistentObjectPresenter extends Component {
	var $obj;
	var $classN;
	var $fieldNames;
	#@use_mixin EditorComponent@#
    function PersistentObjectPresenter(&$object, $fields=null) {
		$this->obj =& $object;
		$this->classN = getClass($object);
		if ($fields===null) {
    		$this->fieldNames = $this->obj->allFieldNames();
		} else {
			$ks = array_keys($fields);
			if (is_object($fields[$ks[0]]) && is_subclass_of($fields[$ks[0]],'DataField')){
				$this->fieldNames =& array_map(create_function('$field','return $field->colName;'),$fields);
			} else {
				$this->fieldNames =& $fields;
			}
		}
    	parent::Component();
    }

    function initialize(){
    	$this->addFields();
    }

    function addFields() {
    	$obj =& $this->obj;

    	$fields =& $obj->fieldsWithNames($this->fieldNames);
       	foreach(array_keys($fields) as $f2){
    		$this->addFieldComponent($this->addField($fields[$f2]), $f2, $fields[$f2]->displayString);
       	}
    }

    function &addField(&$field){
//		$fc =& new FieldValueComponent;
		$fieldComponent = & $this->factory->createFor($field);
//		$fc->addComponent($fieldComponent, 'value');
//		$fc->addComponent(new Label($field->displayString), 'fieldName');
		return $fieldComponent;
    }
}

class FieldValueComponent extends Component{}
?>