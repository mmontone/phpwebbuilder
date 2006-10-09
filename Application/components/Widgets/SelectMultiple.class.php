<?php

class SelectMultiple extends Widget {
	var $options;
	var $displayF;
	var $opts = array();
	var $size = 1;

	function SelectMultiple(&$value_model, &$collection, $displayF=null) {
    	parent::Widget($value_model);
    	$this->options =& $collection;

    	if ($displayF!=null){
    		$this->displayF=$displayF;
    	} else if (is_object($collection->first())){
    		$this->displayF =& new FunctionObject($this, 'printObject');
    	} else {
    		$this->displayF =& new FunctionObject($this, 'printPrimitive');
    	}

    	$collection->addInterestIn('changed', new FunctionObject($this, 'updateViewFromCollection'));

    	/*
    	if ($this->options->isEmpty()) {
    		$this->setValueIndex($i = 0);
    	}*/
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
	//TODO Remove view
    function updateViewFromCollection() {
		//print_backtrace('Options changed');
		$v =& $this->view;
		$cn =& $this->opts;
		$ks = array_keys($cn);
		foreach($ks as $k){
			$v->removeChild($cn[$k]);
		}
		$cn=array();
		$this->viewHandler->initializeView(&$v);
	}

    function setSize($size) {
		$this->size = $size;
	}

	function getSize() {
		return $this->size;
	}
	//TODO Remove view

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

	function viewUpdated($new_value) {
		if ($new_value == '') {
			$this->setValue(new Collection);
		}
		else {
			$selected = explode(',', $new_value);
			$newitems =& new Collection;

			foreach ($selected as $selected) {
				$newitems->add($this->options->at((int) $selected));
			}

			$this->setValue($newitems);
		}
	}
}
?>