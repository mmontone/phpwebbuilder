<?php

class Select extends FormComponent {
	var $options;
    function Select(&$value_model, &$collection) {
    	parent::FormComponent($value_model);
    	$this->options =& $collection;
    	$collection->addEventListener(array('changed'=>'updateViewFromCollection'), $this);
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
    function initializeView(&$view){
		$view->setTagName('select');
		$this->appendOptions($view);
	}

	function appendOptions(&$view) {
		$i=0;
		$this->options->elements->map(
			lambda('&$elem',
			'$option =& new XMLNodeModificationsTracker(\'option\');
			$option->setAttribute(\'value\', $i);
			$option->appendChild(new XMLTextNode($elem));
			$this->opts[$i] =& $option;
			$view->appendChild($option);
			$i++;', get_defined_vars()));
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