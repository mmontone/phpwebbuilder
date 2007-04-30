<?php

class CollectionElementChooser extends CollectionNavigator {

	function initialize() {
		$this->addComponent(new ActionLink($this, 'newObject', 'New', $n = null), 'new');
		parent::initialize();
		$this->refresh();
	}

	function newObject(&$n) {
		$obj = & new $this->classN;
		$ec =& new PersistentObjectEditor($obj);
    	$ec->registerCallback('refresh', callback($this, 'refresh'));
    	$ec->registerCallback('object_edited', new FunctionObject($this,'objectEdited'));
		$this->call($ec);
	}
    #@php5
	function objectEdited(&$object) {
		$db =& DBSession::Instance();
		$db->beginTransaction();
		try {
            $ex =& $db->save($object);
            $this->refresh();
            $db->commit();
        }
		catch (Exception $ex)
        {
			$db->rollback();
			$dialog =& ErrorDialog::Create($ex->getMessage());
			$dialog->onAccept(new FunctionObject($this, 'doNothing'));
			$this->call($dialog);
		}
    }//@#

    #@php4
    function objectEdited(&$object) {
    	$db =& DBSession::Instance();
        $db->beginTransaction();
        $ex =& $db->save($object);
        if (is_exception($ex))
        {
            $db->rollback();
            $dialog =& ErrorDialog::Create($ex->getMessage());
            $dialog->onAccept(new FunctionObject($this, 'doNothing'));
            $this->call($dialog);
        }
        else {
        	$this->refresh();
            $db->commit();
        }
    }//@#

	function &addLine(&$obj) {
		$fc = & new PersistentObjectViewer($obj, $this->fields);
		$fc->addComponent(new CommandLink(array('text' => 'View', 'proceedFunction' => new FunctionObject($this, 'viewObject', array('object' => & $obj)))),'viewer');
		$fc->addComponent(new ActionLink($this, 'selectObject', 'Select', $obj), 'select');
		return $fc;
	}

	function viewObject($params) {
		$obj =& $params['object'];
		$viewer =& new PersistentObjectViewer($obj);
    	//$viewer->registerCallback('refresh', new FunctionObject($this, 'refresh'));
    	$this->call($viewer);
	}

	function selectObject(&$obj){
		$this->callbackWith('selected', $obj);
	}
}
?>
