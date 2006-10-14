<?php

class SelectHTMLHandler extends WidgetHTMLHandler{
	var $opts = array();
    function initializeDefaultView(&$view){
		$view->setTagName('select');
		$view->setAttribute('size', (string) $this->component->getSize());
		$view->setAttribute('style', 'overflow:4;');
	}
	function prepareToRender(){
		parent::prepareToRender();
		$index =& $this->component->getValueIndex();
		$this->valueChanged($this->component->value_model, $n=null);
	}

	function initializeView(&$v){
		$this->appendOptions($v);
	}
	function setComponent(&$component){
	   	parent::setComponent(&$component);
	   	$component->options->addEventListener(array('changed'=>'updateFromCollection'), $this);
	}
	function valueChanged(&$value_model, &$params) {
		if ($this->view){
			if ($this->component->selected_index != -1) {
				$this->opts[$this->component->selected_index]->removeAttribute('selected');
			}
			$index = $this->component->getValueIndex();
			if ($this->opts[$index] !== null) {
				$this->opts[$index]->setAttribute('selected', 'selected');
			}
			$this->component->selected_index =& $this->component->getValueIndex();
			$this->redraw();
		}
	}
	function updateFromCollection() {
		$this->updateViewFromCollection($this->view);
	}
	function updateViewFromCollection(&$v) {
		$v =& $this->view;
		$cn =& $this->opts;
		$ks = array_keys($cn);
		foreach($ks as $k){
			$v->removeChild($cn[$k]);
		}
		$cn = array();
		$this->initializeView(&$v);
		$this->redraw();
	}
	function appendOptions(&$view) {
		$i=0;
		$self =& $this;
		$this->component->options->map(
			$f = lambda('&$elem',
			'$option =& new XMLNodeModificationsTracker(\'option\');
			$option->setAttribute(\'value\', $i);
			$option->appendChild(new XMLTextNode($self->component->displayElement($elem)));
			$self->opts[$i] =& $option;
			$view->appendChild($option);
			$i++;', get_defined_vars()));
		delete_lambda($f);
	}

}
?>