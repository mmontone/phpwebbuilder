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
    	$this->addComponent(new Text(new ValueHolder($obj->displayString())), 'className');
    	$this->addComponent(new Text(new ValueHolder($obj->id->value)), 'id');
		$fs = & $obj->allIndexFields();
    	$factory =& new ShowComponentFactory;
		foreach ($fs as $f) {
    		$fc =& new Obj;
    		$this->addComponent($fc);
    		$fc->addComponent($factory->createFor($f), 'value');
       	}
    }
}
?>