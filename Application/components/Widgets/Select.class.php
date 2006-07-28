<?php

class Select extends Widget {
	var $options;
	var $displayF;
	var $opts = array();
	var $selected_index = -1;

    function Select(&$value_model, &$collection, $displayF=null) {
    	parent::Widget($value_model);
    	$this->options =& $collection;
    	if ($displayF!=null){
    		$this->displayF=$displayF;
    	} else if (isPWBObject($collection->first())){
    		$this->displayF =& lambda('&$e', 'return $e->indexValues();', $a = array());
    	} else {
    		$this->displayF =& lambda('&$e', 'return $e;', $a = array());
    	}
    	$collection->addEventListener(array('changed'=>'updateViewFromCollection'), $this);
    	if (($this->getValueIndex() == -1) and (!$this->options->isEmpty())) {
    		$this->setValueIndex($i = 0);
    	}
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

	function displayElement(&$e){
		$f =& $this->displayF;
		return $f($e);
	}

	function valueChanged(&$value_model, &$params) {
		if ($this->view){
			$this->opts[$this->selected_index]->removeAttribute('selected');
			$this->opts[$this->getValueIndex()]->setAttribute('selected', 'selected');
			$this->view->redraw();
		}
	}

	function &getValueIndex() {
		return $this->options->indexOf($this->getValue());
	}

	function setValueIndex(&$index){
		$this->selected_index =& $index;
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