<?php
class WidgetXULHandler extends WidgetHTMLHandler{
	function updateDisabled(&$vh){
		if ($vh->getValue()) {
			$this->view->setAttribute('disabled','true');
		} else {
			$this->view->removeAttribute('disabled');
		}
	}
	function updateEvent(&$col, &$ev){
		if ($ev[0]=='onclick' || $ev[0]=='onchanged'){
			$this->view->setAttribute('oncommand', $ev[1]);
			$this->view->setAttribute($ev[0], $ev[1]);
		} else {
			$this->view->setAttribute($ev[0], $ev[1]);
		}
	}
}

?>