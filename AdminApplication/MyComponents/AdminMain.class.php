<?php

class AdminMain extends ModuleComponent{
	function getTitle(){
		return sitename.'\'s CMS';
	}
	function &getRootComponent(){
		$comp =& new InitialAdminComponent;
		return $comp;
	}
}

class InitialAdminComponent extends ContextualComponent{
	function initialize(){
		parent::initialize();
		$this->getPWBApplications();
		$null=null;
		$this->addComponent(new Select($null, $this->allPWBApps),'application');
		$this->addComponent(new CommandLink(array('text'=>'Admin Application','proceedFunction'=>new FunctionObject($this,'adminApplication'))));	
	}
	function adminApplication(){
		$this->call(new AdminApplication($this->application->getValue()));
	}
	function getPWBApplications(){
		$PWB_parent_folder = dirname(dirname(dirname(dirname(__FILE__))));
		$pwbapp_apps_dirs = getfilesrec(#@lam $f-> $b = strpos($f, 'Configuration/pwbapp.php')!==false;return $b;@#,
			$PWB_parent_folder);
		foreach($pwbapp_apps_dirs as $pwbapp_apps_dir){
			$pwb_apps_dirs[]=new PWBAppConfig($PWB_parent_folder, substr($pwbapp_apps_dir,0,-strlen('Configuration/pwbapp.php')));
		}
		$this->allPWBApps =& Collection::fromArray($pwb_apps_dirs);
	}
}
class PWBAppConfig{
	var $dirpath;
	var $base_path;
	function PWBAppConfig($base_path, $dirpath){
		$this->dirpath = $dirpath;
		$this->base_path = $base_path;
		$this->readConfig();
	}
	function printString(){
		return @$this->config['global']['app_class'].' - '.substr($this->dirpath,strlen($this->base_path));
	}
	function readConfig (){
		$this->config = @parse_ini_file($this->dirpath.'config.php',true);
	}
}

?>