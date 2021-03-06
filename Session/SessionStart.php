<?php
SessionHandler::setHooks();
session_name(strtolower(app_class));
if (isset($_REQUEST["restart"]) && isset($_COOKIE[session_name()])) {
  PageRenderer::sendRestart();
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
  @session_destroy();
  SessionHandler::setHooks();
  session_regenerate_id();
}
session_start();
if (isset($_REQUEST["reset"])) {
	Application::restart();
}

?>