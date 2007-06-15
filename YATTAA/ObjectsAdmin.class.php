<?php

class ObjectsAdmin extends ContextualComponent {
	var $objects;
	function ObjectsAdmin(&$objects) {
		$this->objects =& $objects;

		parent::ContextualComponent();
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
		$list =& $this->listComponentFor($this->getObjects());
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
		$creator =& $this->getCreatorComponent();
		$creator->addInterestIn('object_edited', new FunctionObject($this, 'objectCreated'));
		$this->call($creator);
	}

	function objectCreated(&$admin,&$obj) {
		$this->saveCreation($obj);
	}

    function saveCreation(&$obj) {$this->triggerEvent('object_created', $obj);}
    function performSave(&$admin,&$obj) {$this->triggerEvent('object_edited', $obj);}
    function performDelete(&$admin,&$obj) {$this->triggerEvent('object_deleted', $obj);}


	function &adminObject(&$obj) {
		$admin =& $this->getAdminComponent($obj);
        $this->call($admin);
		return $admin;
	}

	function &getAdminComponent(&$dt) {
		$admin =& $this->adminComponentFor($dt);
		$admin->addInterestIn('object_edited', new FunctionObject($this, 'performSave'));
		$admin->addInterestIn('object_deleted', new FunctionObject($this, 'performDelete'));
		return $admin;
	}

	function newObjectMessage() {
		return Translator::Translate('New ' . strtolower($this->objects->dataType));
	}

	function listObjectsMessage() {
		return Translator::Translate(ucfirst($this->objects->dataType) . 's');
	}

	function &adminComponentFor(&$object) {
		return mdcompcall('getAdminComponent', array(&$this, &$object));
	}

	function &getCreatorComponent() {
		return mdcompcall('getObjectCreator', array(&$this,&$this->objects));
	}

	function &listComponentFor(&$objects) {
		return mdcompcall('getListComponent',array(&$this, &$objects));
	}
}

#@mixin RootObjectsAdminActions
{
	function saveCreation(&$object) {
		$object->makeRootObject();
		DBSession::commitInTransaction();
		/*$this->adminObject($object);*/
	}
	function performDelete(&$admin,&$object) {
		$object->deleteRootObject();
		DBSession::commitInTransaction();
	}
	function performSave(&$object) {
		DBSession::commitInTransaction();
	}
}
//@#

class RootObjectsAdmin extends ObjectsAdmin{
	#@use_mixin RootObjectsAdminActions@#
}

#@defmdf &getAdminComponent[Component](&$object:Collection<PersistentObject>)
{
		return new ObjectsAdmin($object);
}
//@#

#@defmdf &getAdminComponent[Component](&$object:PersistentObject)
{
		return new ObjectAdmin($object);
}
//@#

?>
