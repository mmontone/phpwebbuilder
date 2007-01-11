<?php

class NavigationLinkHTMLHandler extends CommandLinkHTMLHandler{
	function initializeView(&$view){
		$app =& Application::instance();
		$view->setAttribute('href', $app->toAjax($this->component->app->setLinkTarget($this->component->bookmark, $this->component->params)));
	}
}
?>