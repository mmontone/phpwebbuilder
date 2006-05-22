<?php

require_once 'ObjectComponent.class.php';

class EditObjectComponent extends ObjectComponent {
	var $obj;
	var $classN;
	var $fields;
    function initialize(){
    	$obj =& $this->obj;
    	$this->addComponent(new Label($this->classN), 'className');
    	$this->addComponent(new Label($obj->id->value), 'idN');
    	$this->factory =& new EditComponentFactory;
       	$this->addComponent(new ActionLink($this, 'save', 'save', $n=null), 'save');
       	$this->addComponent(new ActionLink($this, 'deleteObject', 'delete', $n=null), 'delete');
       	$this->addComponent(new ActionLink($this, 'callback', 'cancel', $n), 'cancel');
		parent::initialize();
    }
    function save(){
    	$obj =& $this->obj;
    	$fs =& $this->fieldNames;
    	foreach($fs as $f){
    		$v = $this->fieldComponents[$f]->getValue();
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