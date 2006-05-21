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
    	parent::Component();
    }
    function initialize(){
    	$obj =& $this->obj;
    	$this->addComponent(new Text(new ValueHolder($this->classN), 'className'));
    	$this->addComponent(new Text(new ValueHolder($obj->id->value), 'id'));
    	$fields =& $obj->allFields();
    	$factory =& new EditComponentFactory;
    	foreach($fields as $field){
    		$fc =& new Obj;
    		$this->addComponent($fc, $field->colName);
    		$fc->addComponent(new Text(new ValueHolder($field->displayString)), 'name');
    		$this->fields[$field->colName] = &$factory->createFor($field);
    		$fc->addComponent($this->fields[$field->colName], 'value');
       	}
       	$this->addComponent(new ActionLink($this, 'save', 'save', $n=null), 'save');
       	$this->addComponent(new ActionLink($this, 'deleteObject', 'delete', $n=null), 'delete');
       	$this->addComponent(new ActionLink($this, 'callback', 'cancel', $n), 'cancel');
    }
    function save(){
    	$obj =& $this->obj;
    	$fs =& $obj->allFieldNames();
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

class Obj2 extends Component{}

?>