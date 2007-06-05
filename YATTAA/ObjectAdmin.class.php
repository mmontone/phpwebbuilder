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
        $this->addActionButtons();
	}
	function addActionButtons(){
		$this->addDeleteObjectLink('Borrar');
		$this->addViewObjectLink('Ver');
		$this->addEditObjectLink('Editar');
	}

	function &getModel() {
		return $this->object;
	}

    function &getDeleteFunction($text='Delete') {
		return new FunctionObject($this, 'deleteObject');
	}
	function confirmDeleteMessage() {
		return Translator::TranslateAny($this->object->getAllTypes(), 'Confirm delete ');
	}
	function deleteObject() {
		$question =& QuestionDialog::create($this->confirmDeleteMessage());
		$question->registerCallback('on_yes', new FunctionObject($this, 'deleteConfirmed'));
		$question->registerCallback('on_no', new FunctionObject($this, 'deleteRejected'));
		$this->call($question);
	}


	function changeBody(&$component) {
		$this->addComponent($component, 'body');
	}
	function deleteConfirmed() {
		$this->triggerEvent('object_deleted', $this->object);
	}
    function performSave(&$object) {
    	$this->triggerEvent('object_edited', $this->object);
	}

	function objectDeletedMessage(&$model) {
		return 'The object has been successfully deleted';
	}

	function deleteRejected() {

	}

	function couldNotDelete() {

	}

	function objectDeleted() {
		$this->triggerEvent('object_deleted');
		$this->callback();
	}

	function addDeleteObjectLink($text='Delete') {
		$this->addActionMenu($text,$this->getDeleteFunction($text));
	}

	function addViewObjectLink($text='View') {
		$this->addActionMenu($text,new FunctionObject($this, 'viewObject'));
	}

	function addEditObjectLink($text='Edit') {
		$this->addActionMenu($text,new FunctionObject($this, 'editObject'));
	}

	function removeDeleteObjectLink(&$comp) {
		$comp->deleteComponentAt('delete');
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

	function editObject() {
   	  	$model =& $this->getModel();
		$model->flushChanges();
   	  	$editor =& $this->getObjectEditor($this->getModel());
   	  	$editor->addInterestIn('object_edited', new FunctionObject($this, 'objectEdited'));
    	$editor->addInterestIn('cancel', new FunctionObject($this, 'editionCancelled'));
    	$this->changeBody($editor);
    }

    function &getObjectEditor(&$object) {
    	return mdcompcall('getObjectEditor', array(&$this, &$object));
    }

    function &getObjectViewer(&$object) {
    	return mdcompcall('getObjectViewer', array(&$this, &$object));
    }

    function objectEdited() {
    	$this->saveEditions();
    	$this->viewObject();
    	$this->triggerEvent('object_edited', $this->object);
    }
	function saveEditions(){DBSession::commitInTransaction();}
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