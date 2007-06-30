<?php

  /*
   ObjectsAdmin are components designed with the purpose of managing collections of objects
   in a default way.
   They receive a collection of objects. The collection can be a CollectionField or a RootObjectCollection.
  */

  /* Notes: reasons why I use explicit functions instead of events (see onEditionDo, onDeletionDo protocol) is
   that I don't want ObjectsAdmin do too much. I want the admin to be in control, and events don't fully let us (specially because we
   may not know when events are going to be executed).
   With events, the editor says: "I don't know how to goon, let's trigger an event an wait for intructions"
   Without events, the editor says: "Tell me what to do when a I have edited the object and don't worry, I'm in charge"
  */
class ObjectsAdmin extends ContextualComponent {
	var $objects;
	var $options;

	function ObjectsAdmin(&$objects, $options = array()) {
		$this->objects =& $objects;
	  $this->options = $this->getDefaultOptions();
	  $this->setOptions($options);
	  parent::ContextualComponent();
	}

	function getDefaultOptions() {
	  return array('commit' => true, 'inform_creation_success' => true);
	}

	function setOptions($options) {
	  foreach($this->options as $key => $value) {
	    $this->options[$key] = $value;
	  }
	}

	function getTitle(){
		return $this->listObjectsMessage();
	}

	function setTitle($title) {
	  $this->addComponent(new Label($title), 'admin_title');
	}

	function initialize() {
		$this->displayList();
		$this->addActionMenu($this->newObjectMessage(), new FunctionObject($this, 'newObject'));
	}

	function &getObjects() {
		return $this->objects;
	}

	function changeBody(&$body) {
		$this->addComponent($body, 'body');
	}

	function &displayList() {
	  $objs =& $this->getObjects();
	  $list =& $this->listComponentFor($objs->getCollection());
	  $list->registerCallback('element_selected', new FunctionObject($this, 'elementSelected'));
	  $this->changeBody($list);
	  return $list;
	}

	function elementSelected(&$object) {
		$this->adminObject($object);
	}

	function refreshCollection() {
		$this->body->refresh();
	}

	function newObject() {
	  $self =& $this;
	  $creator =& $this->getCreatorComponent();
	  $creator->onCreationDo(new LambdaObject('&$object', '$self->objects->add($object);', get_defined_vars()));
	  $creator->registerCallback('object_edited', new FunctionObject($this, 'displayList'));
	  $this->call($creator);
	}

	function &adminObject(&$obj) {
	  $admin =& $this->getAdminComponent($obj);
	  $self =& $this;
	  $admin->onEditionDo(new LambdaObject('&$object', '$self->doNothing();', get_defined_vars()));
	  $admin->onDeletionDo(new LambdaObject('&$object', '$self->objects->remove($object);', get_defined_vars()));
	  $this->call($admin);
	  return $admin;
	}

	function &getAdminComponent(&$dt) {
		$admin =& $this->adminComponentFor($dt);
		return $admin;
	}

	function newObjectMessage() {
		$col =& $this->objects->getCollection();
	  return Translator::Translate('New ' . strtolower($col->dataType));
	}

	function listObjectsMessage() {
		$col =& $this->objects->getCollection();
	  return Translator::Translate(ucfirst($col->dataType) . 's');
	}

	function &adminComponentFor(&$object) {
		return mdcompcall('getAdminComponent', array(&$this, &$object));
	}

	function &getCreatorComponent() {
	  $collection =& $this->objects->getCollection();
	  return mdcompcall('getObjectCreator', array(&$this,&$collection));
	}

	function &listComponentFor(&$objects) {
		return mdcompcall('getListComponent',array(&$this, &$objects));
	}

	function successfulCreationMessage() {
	  return 'La creación se ha realizado con éxito';
	}
}

#@mixin DontInformCreationSuccess
{

  function creationSuccessful(&$object) {
    $this->adminObject($object);
    $this->triggerEvent('object_created', $object);
  }
}//@#

// This mixins can be applied to both ObjectsAdmins and ObjectAdmins
#@mixin DontCommit
{
  function commitTransaction() {}
}//@#

#@defmdf &getAdminComponent[Component](&$object:Collection<PersistentObject>)
{
		$oa =& new ObjectsAdmin($object);
		return $oa;
}
//@#

#@defmdf &getAdminComponent[Component](&$object:PersistentObject)
{
		$oa =& new ObjectAdmin($object);
		return $oa;
}
//@#

?>