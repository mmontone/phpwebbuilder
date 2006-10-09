<?php

class Text extends Widget {
	// TODO Remove View
	function valueChanged(& $value_model, &$params) {
		if ($this->viewHandler){
			$this->viewHandler->prepareToRender();
			$this->redraw();
		}
	}
		//TODO Remove view
	function setEvents(& $view) {}
}
?>