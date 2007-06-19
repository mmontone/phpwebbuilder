<?php

class ObjectPresenter extends ContextualComponent {
	var $object;
	var $options = array();

	#@use_mixin EditorComponent@#
	  function ObjectPresenter(&$object, $options = array()) {
		$this->object =& $object;
	  $this->options = $this->getDefaultOptions();
		parent::ContextualComponent();
	}

	function setOptions($options) {
	  foreach($this->options as $key => $value) {
	    $this->options[$key] = $value;
	  }
	}

	function getDefaultOptions() {
	  return array();
	}
	function initialize(){
		$this->addDisplayFields();
	}
	function addDisplayFields(){
		$this->addDefaultDisplayFields();
	}
	function getFieldNames(){
		$fields = $this->object->metadata->allFieldNames();
		unset($fields['id']);
		unset($fields['PWBversion']);
		unset($fields['super']);
		unset($fields['refCount']);
		unset($fields['rootObject']);
		return $fields;
	}
	function addDefaultDisplayFields(){
		$fields =& $this->object->fieldsWithNames($this->getFieldNames());
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