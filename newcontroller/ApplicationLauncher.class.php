<?php

class ApplicationLauncher
{
  function launch($application_class) {
    if ($_SESSION['app']['current_app'] == null || $_REQUEST['reset']) {
      trace("Creating the application ".$application_class);
      $app =& new $application_class;
      $_SESSION['app'] = array();
      $_SESSION['app']['current_app'] =& $app;
    }
    else {
      $app =& $_SESSION['app']['current_app']; 
    }
    $app->run();
  }
}
?>