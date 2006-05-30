<?php

class Select extends FormComponent {
	var $options;
    function Select(&$value_model, &$collection) {
    	parent::FormComponent($value_model);
    	$this->options =& $collection;
    	//$collection->addEventListener(array('changed'=>'initializeView'), $this);
    }

    function initializeView(&$view){
		$view->setTagName('select');
		$this->appendOptions($view);
	}

	function appendOptions(&$view) {
		$i=0;
		$es =& $this->options->elements();
		$ks = array_keys($es);
		foreach($ks as $k){
			$elem =& $es [$k];
			$option =& new XMLNodeModificationsTracker('option');
			$option->setAttribute('value', $i);
			$option->appendChild(new XMLTextNode($elem));
			$this->opts[$i] =& $option;
			$view->appendChild($option);
			$i++;
		}
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
		$this->value_model->setValue($pos);
	}
	function prepareToRender(){
		$this->opts[$this->getValueIndex()]->setAttribute('selected', 'selected');
	}
}
?>