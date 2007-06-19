<?php

  /* Possible options:
   "ask_deletion" : when true, ask before deleting. Default: true.
   "commit" : when true, editions are commited to the database. Default: true.

   Mixins:
   DontAskDeletion, DontCommit
  */


class ObjectAdmin extends ContextualComponent {
	var $object;
	var $options;
	var $edit_function;
	var $delete_function;

	function ObjectAdmin(&$object, $options=array()) {
	  $this->object =& $object;
	  $this->options = $this->getDefaultOptions();
	  $this->setOptions($options);

	  parent::ContextualComponent();
	}

	function getDefaultOptions() {
	  return array('commit' => true, 'ask_deletion' => true);
	}

	function setOptions($options) {
	  foreach($this->options as $key => $value) {
	    $this->options[$key] = $value;
	  }
	}

	function onEditionDo(&$function) {
	  $this->edit_function =& $function;
	}

	function onDeletionDo(&$function) {
	  $this->deletion_function =& $function;
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
	  if ($this->options['ask_deletion']) {
	    $question =& QuestionDialog::create($this->confirmDeleteMessage());
	    $question->registerCallback('on_yes', new FunctionObject($this, 'deleteConfirmed'));
	    $question->registerCallback('on_no', new FunctionObject($this, 'deleteRejected'));
	    $this->call($question);
	  }
	  else {
	    $this->deleteConfirmed();
	  }
	}

	function changeBody(&$component) {
		$this->addComponent($component, 'body');
	}

	function deleteConfirmed() {
		$this->callbackWith('object_deleted', $this->object);
	}

	function commitTransaction() {
	  if ($this->options['commit']) {
	    DBSession::commitInTransaction();
	  }
	}

	function objectDeletedMessage(&$model) {
		return 'The object has been successfully deleted';
	}

	function deleteRejected() {

	}

	function couldNotDelete() {

	}

	function objectDeleted() {
	  $this->callbackWith('object_deleted', $this->object);
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
		$this->changeBody($this->getObjectViewer($this->getModel()));
	}

	function editObject() {
   	  	$model =& $this->getModel();
   	  	$editor =& $this->getObjectEditor($this->getModel());
	  $editor->onEditionDo($this->edit_function);
   	  	$editor->registerCallback('object_edited', new FunctionObject($this, 'objectEdited'));
	  $editor->registerCallback('cancel', new FunctionObject($this, 'editionCancelled'));
	  $this->changeBody($editor);
    }

	function objectEdited(&$object) {
	  $this->viewObject();
	}
    function &getObjectEditor(&$object) {
    	return mdcompcall('getObjectEditor', array(&$this, &$object));
    }

    function &getObjectViewer(&$object) {
    	return mdcompcall('getObjectViewer', array(&$this, &$object));
    }

    function editionCancelled() {
    	$this->viewObject();
    }
}

#@mixin InformEdition

#@defmdf &getObjectCreator[Component](&$objects:Collection)
{
	$dt = $objects->getDataType();
	//$obj =& new $dt;
	//return mdcompcall('getObjectEditor',array($_context,$obj));
	return new CommonObjectCreator($dt);
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
}//@#

?>