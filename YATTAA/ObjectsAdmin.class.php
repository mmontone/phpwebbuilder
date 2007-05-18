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
		$this->call($creator);
	}

	function objectCreated(&$obj) {
		$this->adminObject($obj);
	}

	function &adminObject(&$obj) {
		$admin =& $this->getAdminComponent($obj);
        $this->call($admin);
		return $admin;
	}

	function &getAdminComponent(&$dt) {
		$admin =& $this->adminComponentFor($dt);
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
		return mdcompcall('getObjectCreator', array(&$this));
	}

	function &listComponentFor(&$objects) {
		return mdcompcall('getListComponent',array(&$this, &$objects));
	}
}

?>