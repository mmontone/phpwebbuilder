<?php
class ApplicationLauncher {
	function launch($application_class) {
		if ($_SESSION['app']['current_app'] == null||$_REQUEST["reset"]=="yes") {
			$app = & new $application_class;
			$_SESSION['app']['current_app'] = & $app;
		}
		else {
			$app = & $_SESSION['app']['current_app'];
		}
		$app->run();
	}
}
?>