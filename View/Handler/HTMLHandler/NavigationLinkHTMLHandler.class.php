<?php

class NavigationLinkHTMLHandler extends CommandLinkHTMLHandler{
	function initializeView(&$view){
		$view->setAttribute('href', toAjax($this->component->app->setLinkTarget($this->component->bookmark, $this->component->params)));
	}
}
?>