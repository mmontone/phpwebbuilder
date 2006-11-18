<?php

class SelectXULHandler extends SelectHTMLHandler{
	var $opts = array();
    function initializeDefaultView(&$view){
		$view->setTagName('menulist');
		$view->setAttribute('size', (string) $this->component->getSize());
		//$view->setAttribute('editable', 'true');
		$view->appendChild(new XMLNodeModificationsTracker('menupopup'));
	}
	function updateFromCollection() {
		$this->updateViewFromCollection($this->view->first_child());
	}
	function valueChanged(&$value_model, &$params) {
		if ($this->view){
			if ($this->component->selected_index != -1) {
				$this->opts[$this->component->selected_index]->removeAttribute('selected');
			}
			$index = $this->component->getValueIndex();
			if ($this->opts[$index] !== null) {
				$this->opts[$index]->setAttribute('selected', 'true');
			}
			$this->component->selected_index =& $this->component->getValueIndex();
			$this->redraw();
		}
	}
	function appendOptions(&$view) {
		$i=0;
		$self =& $this;
		$this->component->options->map(
			lambda('&$elem',
			'$option =& new XMLNodeModificationsTracker(\'menuitem\');
			$option->setAttribute(\'value\', $i);
			$option->setAttribute(\'label\',$self->component->displayElement($elem)));
			$self->opts[$i] =& $option;
			$view->appendChild($option);
			$i++;', get_defined_vars()));
	}

}
?>