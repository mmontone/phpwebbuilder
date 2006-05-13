<?php
class ApplicationLauncher {
	function launch($application_class) {
		if ($_SESSION[sitename][$application_class] == null||$_REQUEST["reset"]=="yes") {
			$app = & new $application_class;
			$_SESSION[sitename][$application_class] = & $app;
		}
		else {
			$app = & $_SESSION[sitename][$application_class];
		}
		$app->run();
	}
}
?>