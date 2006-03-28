<?php

class ApplicationLauncher
{
	function launch($application_class) {
		$app =& new $application_class;
        $_SESSION['app'] = array();
		$_SESSION['app']['current_app'] =& $app;
        $app->run();
	}
}

?>