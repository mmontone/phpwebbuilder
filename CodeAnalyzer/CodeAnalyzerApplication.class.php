<?php
class CodeAnalyzerApplication extends Application {
	function & setRootComponent() {
		return new CodeAnalyzer;
	}

	function loadTemplates() {
		$this->viewCreator->loadTemplatesDir(pwbdir . '/CodeAnalyzer/templates');
	}

	function addStyleSheets() {
		$this->addStyleSheet(pwb_url . '/CodeAnalyzer/templates/codeanalyzer.css');
	}
}
?>