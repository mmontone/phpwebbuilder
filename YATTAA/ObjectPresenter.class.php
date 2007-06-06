<?php

class ObjectPresenter extends ContextualComponent {
	var $object;
	#@use_mixin EditorComponent@#
	function ObjectPresenter(&$object) {
		$this->object =& $object;

		parent::ContextualComponent();
	}
	function initialize(){
		$this->addDisplayFields();
	}
	function addDisplayFields(){
		$this->addDefaultDisplayFields();
	}
	function addDefaultDisplayFields(){
		$fields =& $this->object->fieldsWithNames($this->object->metadata->allFieldNames());
		unset($fields['id']);
		unset($fields['PWBversion']);
		unset($fields['super']);
		unset($fields['refCount']);
		unset($fields['rootObject']);
       	foreach(array_keys($fields) as $f2){
    		$this->addFieldComponent($this->chooseFieldDisplayer($fields[$f2]), $f2);
       	}
	}
	function getTitle(){
		return $this->object->printString();
	}
	function setTitle($title) {
		$this->addComponent(new Label($title), 'presenter_title');
	}

	function setModel(&$object) {
		$this->object =& $object;
	}

	function &getModel() {
		return $this->object;
	}
}

?>