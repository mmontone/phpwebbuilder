<?php

require_once 'PersistentObjectPresenter.class.php';

class PersistentObjectViewer extends PersistentObjectPresenter {

    function initialize(){
    	$obj =& $this->obj;
    	//$this->addComponent(new Label($this->classN), 'className');
    	//$this->addComponent(new Label($obj->id->getValue()), 'idN');
    	$this->factory =& new ViewerFactory;
       	$this->addComponent(new ActionLink($this, 'deleteObject', 'delete', $n=null), 'delete');
       	$this->addComponent(new ActionLink($this, 'goback', 'goback', $n), 'goback');
		parent::initialize();
    }

    function goback() {
    	$this->callback();
    }

    function deleteObject(&$fc) {
		$this->call(new QuestionDialog('Are you sure that you want to delete the object?', array('on_yes' => new FunctionObject($this, 'deleteConfirmed', $fc), 'on_no' => new FunctionObject($this, 'deleteRejected')), $fc));
	}

	function deleteConfirmed(&$fc) {
		$ok = $fc->obj->delete();
		$this->refresh();
	}

	function deleteRejected() {

	}
}

?>