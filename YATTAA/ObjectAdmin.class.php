<?php
class ObjectAdmin extends ContextualComponent {
	var $object;

	function ObjectAdmin(&$object) {
		$this->object =& $object;

		parent::ContextualComponent();
	}
	function getTitle(){
		return $this->object->printString();
	}
	function initialize() {
	   	$aspect =& new AspectAdaptor($this->object, array('get' => 'printString'));
        $aspect->setModelSendsUpdates(true);
        $this->addComponent(new Text($aspect), 'model_string');
        $this->viewObject();
	}

	function &getModel() {
		return $this->object;
	}

	function &getGCObjectLink($text='GC') {
        return new CommandLink(array('text' => $text, 'proceedFunction' => new FunctionObject($this, 'gcObject')));
    }

    function &getDeleteFunction($text='Delete') {
		return new FunctionObject($this, 'deleteObject');
	}

	function &getDeleteLogicallyFunction($text='Delete logically') {
		return new FunctionObject($this, 'deleteObjectLogically');
	}

	function &getUndeleteLogicallyFunction($text='Undelete logically') {
		return new FunctionObject($this, 'undeleteObjectLogically');
	}

	/*
	function &getJSDeleteLink($text='Delete') {
		$delete_dialog =& new JSQuestionDialog('¿Are you sure you want to delete it?');
		$delete_dialog->registerCallback('on_yes', new FunctionObject($this, 'deleteConfirmed'));
		$delete_dialog->registerCallback('on_no', new FunctionObject($this,'deleteRejected'));
		return new JSCommandLink(array('text' => $text, 'target' => &$delete_dialog));
	}
	*/

	function confirmDeleteMessage() {
		return '¿Está seguro de querer borrar el '.getClass($this->object).'?';
	}

	function confirmLogicDeleteMessage() {
		return '¿Está seguro de querer borrar lógicamente el '.getClass($this->object).'?';
	}

	function confirmLogicUndeleteMessage() {
		return '¿Está seguro de que quiere recuperar el '.getClass($this->object).'?';
	}

    function gcObject() {
        $question =& newQuestionDialog($this->confirmGCObjectMessage());
        $question->registerCallback('on_yes', new FunctionObject($this, 'gcConfirmed'));
        $question->registerCallback('on_no', new FunctionObject($this, 'gcRejected'));
        $this->call($question);
    }

	function deleteObject() {
		$question =& newQuestionDialog($this->confirmDeleteMessage());
		$question->registerCallback('on_yes', new FunctionObject($this, 'deleteConfirmed'));
		$question->registerCallback('on_no', new FunctionObject($this, 'deleteRejected'));
		$this->call($question);
	}

	function deleteObjectLogically() {
		$question =& newQuestionDialog($this->confirmLogicDeleteMessage());
		$question->registerCallback('on_yes', new FunctionObject($this, 'logicDeleteConfirmed'));
		$question->registerCallback('on_no', new FunctionObject($this, 'logicDeleteRejected'));
		$this->call($question);
	}

	function undeleteObjectLogically() {
		$question =& newQuestionDialog($this->confirmLogicUndeleteMessage());
		$question->registerCallback('on_yes', new FunctionObject($this, 'logicUndeleteConfirmed'));
		$question->registerCallback('on_no', new FunctionObject($this, 'logicUndeleteRejected'));
		$this->call($question);
	}

	function changeBody(&$component) {
		$this->addComponent($component, 'body');
	}

	#@php4
    function deleteConfirmed() {
		$db =& DBSession::instance();
		$db->beginTransaction();

		$ex =& $db->delete($this->object);

		if (is_exception($ex)) {
			$db->rollBack();
			$error_dialog =& newErrorDialog($ex->getMessage());
			$error_dialog->onAccept(new FunctionObject($this, 'couldNotDelete'));
			$this->call($error_dialog);
		}
		else {
			$db->commit();
			$dialog =& newNotificationDialog($this->objectDeletedMessage($this->getModel()));
			$dialog->onAccept(new FunctionObject($this, 'objectDeleted'));
			$this->call($dialog);
		}
	}//@#

    #@php5
    function deleteConfirmed() {
        $db =& DBSession::instance();
        $db->beginTransaction();

        try {
            $db->delete($this->object);
            $db->commit();
            $dialog =& newNotificationDialog($this->objectDeletedMessage($this->getModel()));
            $dialog->onAccept(new FunctionObject($this, 'objectDeleted'));
            $this->call($dialog);
        }
        catch (Exception $ex) {
            $db->rollBack();
            $error_dialog =& newErrorDialog($ex->getMessage());
            $error_dialog->onAccept(new FunctionObject($this, 'couldNotDelete'));
            $this->call($error_dialog);
        }
    }//@#

    function gcConfirmed() {
        $db =& DBSession::instance();
        $db->beginTransaction();

        try {
            $db->delete($this->object);
            $db->commit();
            $dialog =& newNotificationDialog($this->objectDeletedMessage($this->getModel()));
            $dialog->onAccept(new FunctionObject($this, 'doNothing'));
            $this->call($dialog);
        }
        catch (Exception $ex) {
            $db->rollBack();
            $error_dialog =& newErrorDialog($ex->getMessage());
            $error_dialog->onAccept(new FunctionObject($this, 'doNothing'));
            $this->call($error_dialog);
        }
    }

    function logicDeleteConfirmed() {
		$db =& DBSession::instance();
		$db->beginTransaction();
		$this->object->deleteLogically();
		$db->commit();
		$dialog =& newNotificationDialog($this->objectDeletedLogicallyMessage($this->getModel()));
		$dialog->onAccept(new FunctionObject($this, 'objectDeletedLogically'));
		$this->call($dialog);
	}

	function logicUndeleteConfirmed() {
		$db =& DBSession::instance();
		$db->beginTransaction();
		$this->object->undeleteLogically();
		$db->commit();
		$dialog =& newNotificationDialog($this->objectUndeletedLogicallyMessage($this->getModel()));
		$dialog->onAccept(new FunctionObject($this, 'objectUndeletedLogically'));
		$this->call($dialog);
	}

	function objectDeletedMessage(&$model) {
		return 'The object has been successfully deleted';
	}

	function objectDeletedLogicallyMessage(&$model) {
		return 'The object has been successfully deleted logically';
	}

	function objectUndeletedLogicallyMessage(&$model) {
		return 'The object has been successfully undeleted logically';
	}

	function deleteRejected() {

	}

	function logicDeleteRejected() {

	}

	function logicUndeleteRejected() {

	}

	function couldNotDelete() {

	}

	function objectDeleted() {
		$this->callback('object_deleted');
	}

	function objectDeletedLogically() {
		$this->callback('object_deleted_logically');
	}

	function objectUndeletedLogically() {
		$this->callback('object_undeleted_logically');
	}

	function addDeleteObjectLink($text='Delete') {
		if (constant('delete_enabled')) {
			$this->addActionMenu($text,$this->getDeleteFunction($text));
		}
	}

	function addDeleteObjectLogicallyLink($text='Delete') {
		$this->addActionMenu($text,$this->getDeleteLogicallyFunction($text));
	}

	function addUndeleteObjectLogicallyLink($text='Undelete') {
		$this->addActionMenu($text,$this->getUndeleteLogicallyFunction($text));
	}

	function addViewObjectLink($text='View') {
		$this->addActionMenu($text,new FunctionObject($this, 'viewObject'));
	}

	function addEditObjectLink($text='Edit') {
		$this->addActionMenu($text,new FunctionObject($this, 'editObject'));
	}

	function removeDeleteObjectLink(&$comp) {
		if (constant('delete_enabled')) {
			$comp->deleteComponentAt('delete');
		}
	}

	function removeDeleteObjectLogicallyLink(&$bar) {
		$bar->deleteComponentAt('delete_logically');
	}

	function removeUndeleteObjectLogicallyLink(&$bar) {
		$bar->deleteComponentAt('undelete_logically');
	}

	function removeViewObjectLink(&$comp) {
		$comp->deleteComponentAt('_view');
	}

	function removeEditObjectLink(&$comp) {
		$comp->deleteComponentAt('edit');
	}

	function &getViewLink($text='View') {
		return new CommandLink(array('text' => $text, 'proceedFunction' => new FunctionObject($this, 'viewObject')));
	}

	function &getEditLink($text='Edit') {
		return new CommandLink(array('text' => $text, 'proceedFunction' => new FunctionObject($this, 'editObject')));
	}

	function viewObject() {
		$model =& $this->getModel();
		$model->flushChanges();
		$this->changeBody($this->getObjectViewer($this->getModel()));
	}

	function checkDeleteObjectLogicallyPermissions() {
		return !$this->object->isDeleted();
	}

	function checkUndeleteObjectLogicallyPermissions() {
		return $this->object->isDeleted();
	}


	function editObject() {
   	  	$model =& $this->getModel();
		$model->flushChanges();
   	  	$editor =& $this->getObjectEditor($this->getModel());
   	  	$editor->registerCallback('object_edited', new FunctionObject($this, 'objectEdited'));
    	$editor->registerCallback('cancel', new FunctionObject($this, 'editionCancelled'));
    	$this->changeBody($editor);
    }

    function &getObjectEditor(&$object) {
    	return mdcompcall('getObjectEditor', array(&$this, &$object));
    }

    function &getObjectViewer(&$object) {
    	return mdcompcall('getObjectViewer', array(&$this, &$object));
    }

    function objectEdited() {
    	$this->viewObject();
    }

    function editionCancelled() {
    	$this->viewObject();
    }
}

#@defmdf &getObjectCreator[Component](&$objects:Collection)
{
	$dt = $objects->getDataType();
	$obj =& new $dt;
	return mdcompcall('getObjectEditor',array($_context,$obj));
}
//@#
#@defmdf &getObjectViewer[Component](&$object:PersistentObject)
{
		return new ObjectViewer($object);
}
//@#

#@defmdf &getObjectEditor[Component](&$object:PersistentObject)
{
		return new ObjectEditor($object);
}
//@#


?>