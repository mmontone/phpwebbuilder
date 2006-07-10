<?php

class Select extends Widget {
	var $options;
	var $displayF;
	var $opts = array();
    function Select(&$value_model, &$collection, $displayF=null) {
    	parent::Widget($value_model);
    	$this->options =& $collection;
    	if ($displayF!=null){
    		$this->displayF=$displayF;
    	} else if (isPWBObject($collection->first())){
    		$this->displayF =& lambda('&$e', 'return $e->indexValues();', get_defined_vars());
    	} else {
    		$this->displayF =& lambda('&$e', 'return $e;', get_defined_vars());
    	}
    	$collection->addEventListener(array('changed'=>'updateViewFromCollection'), $this);
    }
	function viewUpdated($new_value) {
		$value = & $this->getValueIndex();
		if ($new_value != $value)
			$this->setValueIndex($new_value);
	}
	function updateViewFromCollection(){
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
			lambda('&$elem',
			'$option =& new XMLNodeModificationsTracker(\'option\');
			$option->setAttribute(\'value\', $i);
			$option->appendChild(new XMLTextNode($self->displayElement($elem)));
			$self->opts[$i] =& $option;
			$view->appendChild($option);
			$i++;', get_defined_vars()));
	}
	function displayElement(&$e){
		$f =& $this->displayF;
		return $f($e);
	}
	function valueChanged(&$value_model, &$params) {
		if ($this->view){
			$this->opts[$params['old_value']]->removeAttribute('selected');
			$this->opts[$params['value']]->setAttribute('selected', 'selected');
			$this->view->redraw();
		}
	}
	function &getValue(){
		return $this->options->at($this->getValueIndex());
	}
	function getValueIndex(){
		return parent::getValue();
	}
	function setValue(&$v){
		$pos = $this->options->indexOf($v);
		$this->setValueIndex($pos);
	}
	function setValueIndex(&$v){
		parent::setValue($v);
	}
	function prepareToRender(){
		$this->opts[$this->getValueIndex()]->setAttribute('selected', 'selected');
	}
}
?>