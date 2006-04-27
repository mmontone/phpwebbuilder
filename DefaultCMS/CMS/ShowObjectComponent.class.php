<?php

class ShowObjectComponent extends Component {
	var $obj;
	var $class;
    function ShowObjectComponent(&$class, $id=0) {
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
    	$fs =& $obj->indexFields;
    	foreach($fs as $f){
    		$fc =& new Obj;
    		$this->add_component($fc);
    		$fc->add_component(new Text($obj->$f->value), 'value');
       	}
    }
}
?>