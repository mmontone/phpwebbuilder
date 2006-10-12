<?php

class CMSMenuSectionHandler extends ComponentXULHandler{
	function setView(&$view){
		parent::setView($view);
		$view->setAttribute('label', $this->component->secName->getValue());
	}
}

class CMSMenuItemHandler extends WidgetXULHandler{
	function setView(&$view){
		parent::setView($view);
		$view->setAttribute('label', $this->component->textv);
	}
}

?>