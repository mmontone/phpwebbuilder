<?php
class DrPHP extends Application {
	function & setRootComponent() {
		return new CodeAnalyzer;
	}

	function loadTemplates() {
		$this->viewCreator->loadTemplatesDir(pwbdir . '/DrPHP/templates');
	}

	function addStyleSheets() {
		$this->addStyleSheet(pwb_url . '/DrPHP/templates/drphp.css');
	}
}
?>