<?php

class SelectHTMLHandler extends WidgetHTMLHandler{
	var $opts = array();
    function initializeDefaultView(&$view){
		$view->setTagName('select');
		$view->setAttribute('size', (string) $this->component->getSize());
		$view->setAttribute('style', 'overflow:4;');
		$view->appendChild(new XMLTextNode(''));
	}
	function prepareToRender(){
		parent::prepareToRender();
		$this->valueChanged($this->component->value_model, $n=null);
	}

	function initializeView(&$v){
		$this->appendOptions($v);
	}
	function setComponent(&$component){
	   	parent::setComponent($component);
	   	$component->options->addEventListener(array('changed'=>'updateFromCollection'), $this, array('execute once' => true));
	}
	function valueChanged(&$value_model, &$params) {
		if ($this->view){
			if (isset($this->opts[$this->component->selected_index])) {
				$this->opts[$this->component->selected_index]->removeAttribute('selected');
			}
			$index = $this->component->getValueIndex();
			if (isset($this->opts[$index])) {
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
		$this->initializeView($v);
		$this->redraw();
	}
	function appendOptions(&$view) {
		$i=0;
		$self =& $this;
		$this->component->options->map(
			lambda('&$elem',
			'$option =& new XMLNodeModificationsTracker(\'option\');
			$option->setAttribute(\'value\', $i);
			$display = $self->component->displayElement($elem);
			$option->appendChild(new XMLTextNode($display));
			$self->opts[$i] =& $option;
			$view->appendChild($option);
			$i++;return $i;', get_defined_vars()));
	}

}
?>