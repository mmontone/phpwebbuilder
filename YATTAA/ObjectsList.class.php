<?php

class ObjectsList extends CollectionNavigator {
	function initialize() {
		parent::initialize();
		$this->addComponent(new Label($this->getListName()), 'listName' );
	}
	function getListName(){
		return Translator::Translate('List ' .$this->col->getDataType());;
	}
	function &addLine(&$element) {
        $element->addInterestIn('changed', new FunctionObject($this,'refresh'), array('execute once' => true));
        $dti =& $this->getElementFor($element);
		$dti->registerCallback('element_selected', new FunctionObject($this, 'elementSelected'));
		return $dti;
	}

	function &getElementFor(&$element) {
		return mdcompcall('getListElement', array(&$this, &$element));
	}

	function elementSelected(&$element) {
		$this->callbackWith($this->elementSelectedCallback(), $element);
	}

	function elementSelectedCallback() {
		return 'element_selected';
	}

	function onElementSelectedSend($message, &$target) {
		$this->registerCallback($this->elementSelectedCallback(), new FunctionObject($target, $message));
	}
}

class ObjectElement extends ContextualComponent {
	var $element;

	function ObjectElement(&$element) {
		$this->element =& $element;
		parent::ContextualComponent();
	}

	function initialize() {
		$this->addComponent($this->getSelectionLink(), 'select');
	}

	function &getSelectionLink() {
		$sl =& new CommandLink(array('text' => $this->printElement(), 'proceedFunction' => new FunctionObject($this, 'elementSelected')));
		return $sl;
	}

	function printElement() {
		return $this->element->printString();
	}

	function elementSelected() {
		$this->callbackWith($this->elementSelectedCallback(), $this->element);
	}

	function elementSelectedCallback() {
		return 'element_selected';
	}

	function &getElement() {
		return $this->element;
	}
}

#@defmdf &getListElement[ObjectsList](&$object:PersistentObject)
{$oe =& new ObjectElement($object);return $oe;}
//@#

?>