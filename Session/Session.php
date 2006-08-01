<?php
/*
 * Created on 09/07/2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/*Session handling*/

includemodule(pwbdir.'/Model');

require_once 'SessionHandler.class.php';
require_once 'Session.class.php';
require_once 'DBSessionHandler.class.php';

SessionHandler::setHooks();
session_name(strtolower(app_class));
if ($_REQUEST["restart"]=="yes") {
  $sessionid = $_COOKIE[session_name()];
  $orgpath = getcwd();
  chdir(PHP_BINDIR);
  chdir(session_save_path());
  $path = realpath(getcwd()).'/';
  if(file_exists($path.'sess_'.$sessionid)) {
   unlink($path.'sess_'.$sessionid);
  }
  chdir($orgpath);
  session_start();
  session_destroy();
  SessionHandler::setHooks();
  session_regenerate_id();
}
session_start();
if ($_REQUEST["reset"]=="yes" && isset($_SESSION[sitename]) && isset($_SESSION[sitename][app_class])) {
	unset($_SESSION[sitename][app_class]);
}

?>
