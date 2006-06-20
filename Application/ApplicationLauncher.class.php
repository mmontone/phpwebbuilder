<?php
class ApplicationLauncher {
	function launch($application_class) {
		if ($_REQUEST["reset"]=="yes") {
			/*session_destroy();
			session_start();*/
			unset($_SESSION[sitename][$application_class]);
		}
		$app = & Application::getInstanceOf($application_class);
		$app->run();
	}
}
?>