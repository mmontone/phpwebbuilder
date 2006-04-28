<?php

class EditObjectComponent extends Component {
	var $obj;
	var $class;
	var $fields;
    function EditObjectComponent(&$class, $id=0) {
    	if (is_array($class)){
    		$this->obj =& PersistentObject::getWithId($class['ObjType'], $class['ObjID']);
    		$this->class =& $class['ObjType'];
    	} else if (is_object($class)){
    		$this->obj =& $class;
    		$this->class =& get_class($class);
    	} else {
    		$this->obj =& PersistentObject::getWithId($class, $id);
    		$this->class =& $class;
    	}
    	parent::Component();
    }
    function initialize(){
    	$obj =& $this->obj;
    	$this->add_component(new Text($this->class), 'class');
    	$this->add_component(new Text($obj->id->value), 'id');
    	$fs =& $obj->allFieldNames();
    	foreach($fs as $f){
    		$fc =& new Obj;
    		$this->add_component($fc);
    		$fc->add_component(new Text($f), 'name');
    		$fc->add_component(new Input($obj->$f->value), 'value');
       	}
       	$this->add_component(new ActionLink($this, 'save', 'save', $n), 'save');
       	$this->fields =& $fs;
    }
    function save(){
		$this->obj->save();
		$this->triggerEvent('ObjectUpdated', $n=null);
		$this->callback();
    }
}
?>