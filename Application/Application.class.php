<?php

class Application {
	var $viewCreator;
	var $page_renderer;
	var $needView = array ();
	var $historylistener;
	var $config;
	var $translators = array();
	var $commands;


	function Application() {
		Session::setAttribute(getClass($this),$this);
		$this->commands =& new Collection();
		$this->createView();
		$c =& new Component;
		Window::setActiveInstance(new Window($c, 'root'));
		$rc = & $this->setRootComponent();
		#@typecheck $rc:Component@#
		$c->stopAndCall($rc);
	}
	function addWindow(&$win, $pos){
		$this->windows[$pos]=&$win;
		$this->windows[$pos]->createView();
	}
	function createView(){
		$this->page_renderer =& PageRenderer::create($this);
		if (!$this->viewCreator) {
			$this->viewCreator = & new ViewCreator($this);
		}
		$this->initializeStyleSheets();
		$this->initializeScripts();
	}
	function Install() {
		// Return an exception if something goes wrong
 		return false;
	}

	function getName() {
		return ucfirst(getClass($this));
	}

	function getAdminEmail() {
		if (defined('admin_email')) {
			return constant('admin_email');
		}
		return '';
	}

	function pushCommand(&$command){
		$this->commands->push($command);
	}
	function &setRootComponent(){
		trigger_error("Subclass responsibility");
		exit;
	}
	function & getInstanceOf($c) {
		$class = strtolower($c);
		if (!Session::isSetAttribute($class)) {
			$app =& new $class;
		}
		return Session::getAttribute($class);
	}
	function restart(){
		Session::restart();
	}
	/**
	 * Encodes the string una ajax suitable manner
	 */
	function toAjax($s) {
		return $this->page_renderer->toAjax($s);
	}
	function &instance(){
		return Application::getInstanceOf(constant('app_class'));
	}
	function initialRender() {
		$this->page_renderer->firstRender=true;
		$this->render();
	}
	function setTitle($title){
		$this->windows['root']->setTitle($title);
	}

	function getTitle(){return '';}
	function initializeStyleSheets() {
		$this->wholeView->style_sheets = array ();
		$this->addStyleSheet(constant('pwb_url') . 'View/common.css');
		$this->addStyleSheet(constant('pwb_url') . 'View/debug.css', false);
 		$this->addStyleSheet(constant('pwb_url').'lib/modal-message/css/modal-message.css');

		$this->addStyleSheets();
	}

	function initializeScripts() {
		$this->scripts = array ();
		$this->jsscripts = array();
		$this->addScript(constant('pwb_url') . 'lib/common.js');
		$this->addScript(constant('pwb_url') . 'lib/modal-message/pwb/dialogs.js');
		$this->addScript(constant('pwb_url') . 'lib/modal-message/js/ajax.js');
		$this->addScript(constant('pwb_url') . 'lib/modal-message/js/modal-message.js');
		$this->addScript(constant('pwb_url') . 'lib/modal-message/js/ajax-dynamic-content.js');
		$this->page_renderer->initializeScripts($this);
		$this->addScripts();
	}

	function addScripts() {}

	function addStyleSheets() {}

	function addAjaxRenderingSpecificScripts() {
		//$this->addScript(constant('pwb_url') . 'lib/dhtmlHistory.js');
		//$this->addScript(constant('pwb_url') . 'lib/history.js');
		$this->addScript(constant('pwb_url') . 'lib/ajax.js');
	}
	function addXULRenderingSpecificScripts() {
		$this->addScript(constant('pwb_url') . 'lib/ajax.js');
        $this->addScript(constant('pwb_url') . 'lib/xul.js');
	}
	function addStdRenderingSpecificScripts() {
		$this->addScript(constant('pwb_url') . 'lib/std.js');
	}

	function addScript($url) {
		$this->scripts[] = & $url;
	}

	function addStyleSheet($url) {
		if (!is_array($url)) {
			$this->style_sheets[] = array('media'=>'screen','href'=>$url);
		} else {
			$this->style_sheets[] = $url;
		}
	}

	function renderExtraHeaderContent() {}

	function loadTemplates () {
 		if (defined('templates') and constant('templates') == 'disabled') {
 			return;
 		}

 		$templatesdir = $this->getTemplatesDir();
 		$this->viewCreator->loadTemplatesDir($templatesdir);
 	}

 	function getTemplatesDir() {
 		if (!defined('templatesdir')) {
 			return constant('basedir') . 'MyTemplates/';
 		}
 		else {
 			return constant('templatesdir');
 		}
 	}

	function getRealId() {
		return "app".CHILD_SEPARATOR . getClass($this);
	}

	function needsView(& $comp) {
		$pv =& $comp->parentView();
		$this->viewCreator->createElemView($pv,$comp);
	}

	function translate($msg) {
		return Translator::TranslateWith(translator,$msg);
	}
	function launch() {
		$ad =& new ActionDispatcher();
		$window =& $ad->dispatch();
		return $window->render();
	}
	function &getWidgets(){
		$ws=array();
		$this->component->getWidgets($ws);
		return $ws;
	}
	function setLinkTarget($bookmark, $params){
		return UrlManager::setLinkTarget($bookmark, $params);
	}

	function &getRootComponent() {
		return $this->getComponent();
	}
	function triggerEvent($ev){
		$this->$ev();
	}
	function reset_templates(){
		$this->viewCreator->reloadView();
	}
	function defaultTag(){
		$app =& Application::instance();
		return $app->page_renderer->defaultTag();
	}

}

#@mixin DynVars
{
	function setDynVar($name, &$value) {
		$this->dyn_vars[$name] =& $value;
	}

	function &getDynVar($name) {
		if (isset($this->dyn_vars[$name])) {
			return $this->dyn_vars[$name];
		}
		else {
			$parent =& $this->getParent();
			#@check $parent !== null@#
			return $parent->getDynVar($name);
		}
	}
}//@#




?>