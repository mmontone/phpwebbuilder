<?php
class CodeAnalizerApplication extends Application {
	function & setRootComponent() {
		return new CodeAnalizer;
	}

	function loadTemplates() {
		$this->viewCreator->loadTemplatesDir(pwbdir . '/CodeAnalizer/templates/');
	}

	function addStyleSheets() {
		$this->addStyleSheet(pwb_url . '/CodeAnalizer/templates/codeanalizer.css');
	}
}
?>