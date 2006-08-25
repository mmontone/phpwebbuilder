<?php

/*Session handling*/

includemodule(pwbdir.'/Model');

$d = dirname(__FILE__);
require_once $d.'/SessionHandler.class.php';
require_once $d.'/Session.class.php';
require_once $d.'/DBSessionHandler.class.php';

SessionHandler::setHooks();
session_name(strtolower(app_class));
if (isset($_REQUEST["restart"])) {
  $sessionid = $_COOKIE[session_name()];
  $orgpath = getcwd();
  @chdir(PHP_BINDIR);
  @chdir(session_save_path());
  $path = realpath(getcwd()).'/';
  if(file_exists($path.'sess_'.$sessionid)) {
   @unlink($path.'sess_'.$sessionid);
  }
  @chdir($orgpath);
  session_start();
  session_destroy();
  SessionHandler::setHooks();
  session_regenerate_id();
}
session_start();
if (isset($_REQUEST["reset"]) && isset($_SESSION[sitename]) && isset($_SESSION[sitename][app_class])) {
	unset($_SESSION[sitename][app_class]);
}

?>
