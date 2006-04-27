<?php

class ShowCollectionComponent extends Component {
	var $col;
	var $class;
    function ShowCollectionComponent(&$colclass) {
		if (is_object($colclass)){
    		$this->col =& $colclass;
    		$this->class = $colclass->dataType;
    	} else if (is_array($colclass)){
    		$this->col =& new PersistentCollection($colclass["ObjType"]);
    		$this->class = $colclass["ObjType"];
    	} else {
    		$this->col =& new PersistentCollection($colclass);
    		$this->class = $colclass;
    	}
    	parent::Component();
    }
    function initialize(){
    	$objects =& $this->col->objects();
    	$ks =& array_keys($objects);
    	$class =& $this->class;
    	$this->add_component(new Text($class), 'class');
    	$obj =& $objects[0];
    	$fs =& $obj->indexFields;
    	foreach($fs as $f){
    		$fc =& new Obj;
    		$fc->add_component(new Text($f));
    		$this->add_component($fc);
       	}
    	foreach($ks as $k){
    		$fc =& new ShowObjectComponent($objects[$k]);
    		$fc2 =& new EditObjectComponent($objects[$k]);
    		$this->add_component($fc);
    		$fc->add_component(new ActionLink($this, 'editObject', 'Edit', $fc2), 'edit');
       	}
    }
    function editObject(&$fc){
		$this->call($fc);
    }
}

class Obj extends Component{}

?>