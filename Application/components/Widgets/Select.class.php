<?php

class Select extends Widget {
	var $options;
	var $displayF;
	var $opts = array();
	var $selected_index = -1;
	var $size = 1;

    function Select(&$value_model, &$collection, $displayF=null) {
    	parent::Widget($value_model);
    	$this->options =& $collection;

    	if ($displayF!=null){
    		$this->displayF=$displayF;
    	} else if (is_object($collection->first())){
    		$this->displayF =& new FunctionObject($this, 'printObject');
    	} else {
    		$this->displayF =& new FunctionObject($this, 'printPrimitive');
    	}

    	$collection->addEventListener(array('changed'=>'updateViewFromCollection'), $this);
    	if (($this->getValueIndex() == -1) and (!$this->options->isEmpty())) {
    		$this->setValueIndex($i = 0);
    	}
    }

    function displayElement(&$e){
		$f =& $this->displayF;
		//return $f($e);
		return $f->callWith($e);
	}

	function &printObject(&$object) {
		return $object->printString();
	}

	function &printPrimitive(&$primitive) {
		return $primitive;
	}

    function viewUpdated($new_value) {
		$value = & $this->getValueIndex();
		if ($new_value != $value)
			$this->setValueIndex($new_value);
	}

	function updateViewFromCollection() {
		$v =& $this->view;
		$cn =& $this->opts;
		$ks = array_keys($cn);
		foreach($ks as $k){
			$v->removeChild($cn[$k]);
		}
		$this->initializeView(&$v);
	}

    function initializeDefaultView(&$view){
		$view->setTagName('select');
		$view->setAttribute('size', (string) $this->getSize());
		$view->setAttribute('style', 'overflow:4;');
	}

	function setSize($size) {
		$this->size = $size;
	}

	function getSize() {
		return $this->size;
	}

	function initializeView(&$v){
		$this->appendOptions($v);
	}

	function appendOptions(&$view) {
		$i=0;
		$self =& $this;
		$this->options->map(
			$f = lambda('&$elem',
			'$option =& new XMLNodeModificationsTracker(\'option\');
			$option->setAttribute(\'value\', $i);
			$option->appendChild(new XMLTextNode($self->displayElement($elem)));
			$self->opts[$i] =& $option;
			$view->appendChild($option);
			$i++;', get_defined_vars()));
		delete_lambda($f);
	}

	function valueChanged(&$value_model, &$params) {
		if ($this->view){
			if ($this->selected_index != -1) {
				$this->opts[$this->selected_index]->removeAttribute('selected');
			}
			$index = $this->getValueIndex();
			if ($this->opts[$index] !== null) {
				$this->opts[$index]->setAttribute('selected', 'selected');
			}
			$this->selected_index =& $this->getValueIndex();
			$this->view->redraw();
		}
	}

	function &getValueIndex() {
		return $this->options->indexOf($this->getValue());
	}

	function setValueIndex(&$index){
		$this->setValue($this->options->at($index));
	}

	function prepareToRender(){
		parent::prepareToRender();
		$index =& $this->getValueIndex();
		if ($this->opts[$index] != null) {
			$this->opts[$index]->setAttribute('selected', 'selected');
		}
	}
}

?>