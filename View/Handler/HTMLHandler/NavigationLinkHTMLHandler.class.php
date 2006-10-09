<?php

class NavigationLinkHTMLHandler extends ComandLinkHTMLHandler{
	function initializeView(&$view){
		$view->setAttribute('href', toAjax($this->app->setLinkTarget($this->bookmark, $this->params)));
	}
}
?>