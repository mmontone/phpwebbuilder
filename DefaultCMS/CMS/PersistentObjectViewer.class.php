<?php

require_once 'PersistentObjectPresenter.class.php';

class PersistentObjectViewer extends PersistentObjectPresenter {
    function initialize(){
    	$obj =& $this->obj;
    	//$this->addComponent(new Label($this->classN), 'className');
    	//$this->addComponent(new Label($obj->id->getValue()), 'idN');
    	$this->factory =& new ViewerFactory;

		$class = getClass($obj);

		PermissionChecker::addComponent($this,
					new ActionLink($this, 'deleteObject', 'delete', $obj),
					new FunctionObject(User::logged(), 'hasPermissions', array(getClass($obj).'=>Delete', '*',getClass($obj).'=>*'))
					,'delete');
       	$this->addComponent(new ActionLink($this, 'cancel', 'cancel', $n=null), 'cancel');
       	PermissionChecker::addComponent($this,
					new CommandLink(array('text' => 'Edit', 'proceedFunction' => new FunctionObject($this, 'editObject', array('object' => & $obj)))),
					new FunctionObject(User::logged(), 'hasPermissions', array($class.'=>Edit', '*',$class.'=>*'))
					,'edit');
       	//$this->addComponent(new )
		parent::initialize();
    }

    function editObject($params) {
		$obj =& $params['object'];
		$msg =& $params['msg'];
		$ec =& new PersistentObjectEditor($obj);
    	$ec->registerCallback('object_edited', new FunctionObject($this, 'objectEdited'));
    	//$ec->registerCallback('refresh', new FunctionObject($this, 'refresh'));
    	if (!empty($msg)){
    		$ec->displayValidationErrors($msg);
    	}
    	$this->call($ec);
	}

    function objectEdited(&$object) {
		$ok = $object->save();

		if (!$ok){
			$this->editObject(array('object'=> &$object, 'msg' =>array('version'=>new ValidationException(array('message'=>'This object has been modified by another user')))));
		}
	}

	function cancel() {
		$this->callback('refresh');
	}
}

?>