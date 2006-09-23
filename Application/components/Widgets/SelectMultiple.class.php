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

    	$collection->addEventListener(array('changed'=>'updateViewFromCollection'), $this);
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

    function updateViewFromCollection() {
		$v =& $this->view;
		$cn =& $this->opts;
		$ks = array_keys($cn);
		foreach($ks as $k){
			$v->removeChild($cn[$k]);
		}
		$this->initializeView(&$v);
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

	function viewUpdated($new_value) {
		if ($new_value == '') {
			$this->setValue(new Collection);
		}
		else {
			$selected = explode(',', $new_value);
			$newitems =& new Collection;

			foreach ($selected as $selected) {
				$selected = (int) $selected;
				$newitems->add($this->options->at($selected));
			}

			$this->setValue($newitems);
		}
	}

	function valueChanged(&$value_model, &$params) {
		$elements =& $params->elements();

		foreach (array_keys($this->opts) as $opt) {
			$this->opts[$opt]->removeAttribute('selected');
		}

		foreach(array_keys($elements) as $e) {
			$element =& $elements[$e];
			$opt = $this->options->indexOf($element);
			$this->opts[$opt]->setAttribute('selected', 'selected');
		}

		$this->view->redraw();
	}

    function initializeDefaultView(&$view){
		$view->setTagName('select');
		$view->setAttribute('multiple', 'multiple');
		//$view->setAttribute('size', (string) $this->getSize());
	}
}
?>