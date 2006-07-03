<?php

require_once 'PersistentObjectPresenter.class.php';

class PersistentObjectViewer extends PersistentObjectPresenter {
    function initialize(){
    	$obj =& $this->obj;
    	//$this->addComponent(new Label($this->classN), 'className');
    	//$this->addComponent(new Label($obj->id->getValue()), 'idN');
    	$this->factory =& new ViewerFactory;
       	$this->addComponent(new ActionLink($this, 'callback', 'goback', $n), 'goback');
		parent::initialize();
    }
}

?>