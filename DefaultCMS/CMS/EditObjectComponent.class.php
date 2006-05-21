<?php

class EditObjectComponent extends Component {
	var $obj;
	var $classN;
	var $fields;
    function EditObjectComponent(&$class, $id=0) {
    	if (is_array($class)){
    		$this->obj =& PersistentObject::getWithId($class['ObjType'], $class['ObjID']);
    		$this->classN = $class['ObjType'];
    	} else if (is_object($class)){
    		$this->obj =& $class;
    		$this->classN = get_class($class);
    	} else {
    		$this->obj =& PersistentObject::getWithId($class, $id);
    		$this->classN = $class;
    	}
    	$this->fieldNames =& $this->obj->allFieldNames();
    	parent::Component();
    }
    function initialize(){
    	$obj =& $this->obj;
    	$this->addComponent(new Label($this->classN), 'className');
    	$this->addComponent(new Label($obj->id->value), 'idN');
    	$fields =& $obj->fieldsWithNames($this->fieldNames);
    	$factory =& new EditComponentFactory;
    	$temp = array();
    	foreach($this->fieldNames as $f2){
    		$f =& $temp[$f2];
    		$f = $f2;
    		$field =& $fields[$f];
    		$fc =& new FormComponent($n=null);
    		$this->addComponent($fc, $f);
    		$fc->addComponent(new Text(new ValueHolder($field->displayString)), 'name');
    		$this->fields[$f] = &$factory->createFor($field);
    		$fc->addComponent($this->fields[$f], 'value');
       	}
       	$this->addComponent(new ActionLink($this, 'save', 'save', $n=null), 'save');
       	$this->addComponent(new ActionLink($this, 'deleteObject', 'delete', $n=null), 'delete');
       	$this->addComponent(new ActionLink($this, 'callback', 'cancel', $n), 'cancel');
    }
    function save(){
    	$obj =& $this->obj;
    	$fs =& $this->fieldNames;
    	foreach($fs as $f){
    		$v = $this->fields[$f]->getValue();
    		$obj->$f->setValue($v);
    	}
		$obj->save();
		$this->callback('refresh');
    }
	function deleteObject(&$fc) {
		$this->obj->delete();
		$this->callback('refresh');
	}
}

?>