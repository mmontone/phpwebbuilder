<?php

class HTML extends Widget {
	function HTML($string){
		parent::Widget(new ValueHolder($string));
	}
	// TODO Remove View
	function valueChanged(& $value_model, &$params) {
		if ($this->view){
			$this->viewHandler->prepareToRender();
			$this->redraw();
		}
	}
	function setEvents(& $view) {}
}
?>