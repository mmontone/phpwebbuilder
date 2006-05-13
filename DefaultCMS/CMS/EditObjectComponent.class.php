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
    	$this->add_component(new Text(new ValueHolder($this->classN), 'className'));
    	$this->add_component(new Text(new ValueHolder($obj->id->value), 'id'));
    	$fs =& $obj->allFieldNames();
    	$factory =& new EditComponentFactory;
    	foreach($fs as $f){
    		$fc =& new Obj;
    		$this->add_component($fc, $f);
    		$fc->add_component(new Text(new ValueHolder($fs[$f])), 'name');
    		$this->fields[$f]=&$factory->createFor($obj->$f);
    		$fc->add_component($this->fields[$f], 'value');
       	}
       	$this->add_component(new ActionLink($this, 'save', 'save', $n=null), 'save');
       	$this->add_component(new ActionLink($this, 'deleteObject', 'delete', $n=null), 'delete');
       	$this->add_component(new ActionLink($this, 'callback', 'cancel', $n), 'cancel');
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