<?php

class Application extends ComponentHolder {
	var $wholeView;
	var $viewCreator;
	var $page_renderer;
	var $needView = array ();
	var $historylistener;
	var $config;
	var $translators = array();
	var $commands;
	var $urlManager;

	function Application() {
		$_SESSION[sitename][getClass($this)] = & $this;
		$this->commands =& new Collection();
		$this->urlManager =& new UrlManager($this);
		$page_renderer = constant('page_renderer');
		$this->page_renderer = new $page_renderer;
		$this->createView();
		$rc = & $this->setRootComponent();
		parent :: ComponentHolder($rc, $index = 0, $n = null);
		$rc->linkToApp($this);
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
	function redraw() {
		$this->wholeView->flushModifications();
		$this->wholeView->replaceChild($this->wholeView->first_child(), clone($this->wholeView->first_child()));
	}

	function standardRender() {
		$this->viewCreator->createAllViews();
		$pr =& new StandardPageRenderer();
		$pr->setPage($this, $this->wholeView);
		echo $pr->renderPage($this);
	}
	function & getInstanceOf($c) {
		$class = strtolower($c);
		if (!isset ($_SESSION[sitename][$class])) {
			$_SESSION[sitename][$class] = & new $class;
		}
		return $_SESSION[sitename][$class];
	}
	function restart(){
		unset($_SESSION[sitename]);
	}
	function &instance(){
		return Application::getInstanceOf(app_class);
	}
	function initialRender() {
		$this->page_renderer->firstRender=true;
		$this->render();
	}
	function render() {
		$this->viewCreator->createAllViews();
		$out = $this->page_renderer->render($this);
		echo $out;
		session_write_close();
		return $out;
	}

	function createView() {
		if (!$this->viewCreator) {
			$this->viewCreator = & new ViewCreator($this);
			$this->loadTemplates();
			$this->wholeView = & new XMLNodeModificationsTracker;
			$tc =& new HTMLContainer('',array());
			$tc->setAttribute('class','Component');
			$this->wholeView->appendChild($tc);
			$this->wholeView->controller = & $this;
			$this->wholeView->getTemplatesAndContainers();
			$this->wholeView->jsscripts = array();
			$this->setTitle($this->getTitle());
			$this->initializeStyleSheets();
			$this->initializeScripts();
			$this->page_renderer->setPage($this, $this->wholeView);
			$this->page_renderer->initialRender();
		}
	}
	function setTitle($title){
		$this->wholeView->title=$title;
	}
	function getTitle(){}
	function initializeStyleSheets() {
		$this->wholeView->style_sheets = array ();
		$this->addStyleSheet(pwb_url . '/View/common.css');
		$this->addStyleSheet(pwb_url . '/View/debug.css', false);
		$this->addStyleSheets();
	}

	function initializeScripts() {
		$this->wholeView->scripts = array ();
		$this->addScript(pwb_url . '/lib/common.js');
		$this->page_renderer->initializeScripts($this);
		$this->addScripts();
	}

	function addScripts() {}

	function addStyleSheets() {}

	function addAjaxRenderingSpecificScripts() {
		$this->addScript(pwb_url . '/lib/dhtmlHistory.js');
		$this->addScript(pwb_url . '/lib/history.js');
		$this->addScript(pwb_url . '/lib/ajax.js');
	}
	function addXULRenderingSpecificScripts() {
		$this->addScript(pwb_url . '/lib/ajax.js');
	}
	function addStdRenderingSpecificScripts() {
		$this->addScript(pwb_url . '/lib/std.js');
	}

	function addScript($url) {
		$this->wholeView->scripts[] = & $url;
	}

	function addStyleSheet($url, $enabled=true) {
		$this->wholeView->style_sheets[] = array($url,$enabled);
	}

	function renderExtraHeaderContent() {}

	function & view() {
		return $this->wholeView;
	}

	function loadTemplates () {
 		if (defined('templates') and constant('templates') == 'disabled') {
 			return;
 		}

 		if (!defined('templatesdir')) {
 			$templatesdir= basedir . 'MyTemplates';
 		}
 		else {
 			$templatesdir = templatesdir;
 		}

 		$this->viewCreator->loadTemplatesDir($templatesdir);
 	}

	function getId() {
		return "app";
	}

	function getRealId() {
		return "app".CHILD_SEPARATOR . getClass($this) . CHILD_SEPARATOR."main";
	}

	function needsView(& $comp) {
		//$this->needView[$comp->getId()] = & $comp;
		$this->viewCreator->createElemView($comp->parentView(),$comp);
	}

	function translate($msg) {
		return Translator::TranslateWith(translator,$msg);
	}
	function launch() {
		$ad =& new ActionDispatcher();
		$app =& $ad->dispatch();
		return $app->render();
	}
	function &getWidgets(){
		$ws=array();
		$this->component->getWidgets($ws);
		return $ws;
	}
	function setLinkTarget($bookmark, $params){
		return $this->urlManager->setLinkTarget($bookmark, $params);
	}
	function navigate($bookmark, $params){
		$this->urlManager->navigate($bookmark, $params);
	}
	function goToUrl($url){
		$this->urlManager->goToUrl($url);
	}
	function resetUrl(){
		$this->urlManager->resetUrl();
	}
	function badUrl($bm, $params){
		$this->resetUrl();
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

}
?>