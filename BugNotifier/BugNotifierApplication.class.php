<?php

class BugNotifierApplication extends Application {

    function &setRootComponent() {
		$error_handler_class = 'DevelopApplicationErrorHandler';

		if (defined('error_handler')) {
			$error_handler_class = constant('error_handler');
		}

		return new $error_handler_class;
    }

    function loadTemplates () {
 		$this->viewCreator->loadTemplatesDir(pwbdir . "/BugNotifier/templates");
 		$this->viewCreator->loadTemplatesDir(basedir . "/MyTemplates/");
 	}
 	function addStyleSheets(){
 		$this->addStyleSheet(pwb_url."/BugNotifier/templates/bugnotifier.css");
 	}

 	function setError($error) {
 		$root =& $this->getRootComponent();
 		$root->setError($error);
 	}

 	function setBacktrace($backtrace) {
 		$root =& $this->getRootComponent();
 		$root->setBacktrace($backtrace);
 	}
}

?>