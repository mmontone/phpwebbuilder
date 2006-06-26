<?php
require_once dirname(__FILE__) . '/ComponentHolder.class.php';

class Application extends ComponentHolder {
	var $wholeView;
	var $viewCreator;
	var $page_renderer;
	var $needView = array ();
	var $historylistener;
	var $config;
	var $translator;
	var $commands;
	var $urlManager;
	function Application() {
		$_SESSION[sitename][getClass($this)] = & $this;
		$this->commands =& new Collection();
		$this->urlManager =& new UrlManager($this);
		$rc = & $this->setRootComponent();
		parent :: ComponentHolder($rc, $index = 0, $n = null);
		$rc->linkToApp($this);
		$page_renderer = constant('page_renderer');
		$this->page_renderer = new $page_renderer;
		$this->createView();

	}
	function pushCommand(&$command){
		$this->commands->push($command);
	}
	function setRootComponent(){
		trigger_error("Subclass responsibility");
		exit;
	}
	function redraw() { 
		$this->wholeView->flushModifications();
		$this->wholeView->replaceChild($this->wholeView->first_child(), $other = $this->wholeView->first_child());
	}

	function standardRender() {
		$this->viewCreator->createAllViews();
		$pr =& new StandardPageRenderer();
		$pr->setPage($this->wholeView);
		echo $pr->renderPage($this);
	}
	function & getInstanceOf($class) {
		if (!isset ($_SESSION[sitename][$class])) {
			$_SESSION[sitename][$class] = & new $class;
		}
		return $_SESSION[sitename][$class];
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
		echo $this->page_renderer->render($this);
	}

	function createView() {
		if (!$this->viewCreator) {
			$this->viewCreator = & new ViewCreator($this);
			$this->loadTemplates();
			$this->wholeView = & new XMLNodeModificationsTracker;
			$this->wholeView->controller = & $this;
			$this->wholeView->appendChild($this->component->myContainer());
			$this->wholeView->getTemplatesAndContainers();
			$this->setTitle($this->getTitle());
			$this->initializeStyleSheets();
			$this->initializeScripts();
			$this->page_renderer->setPage($this->wholeView);
		}
	}
	function setTitle($title){
		$this->wholeView->title=$title;
	}
	function getTitle(){}
	function initializeStyleSheets() {
		$this->wholeView->style_sheets = array ();
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
		$this->addScript(pwb_url . '/lib/ajax.js');
	}

	function addStdRenderingSpecificScripts() {
		$this->addScript(pwb_url . '/lib/std.js');
	}

	function addScript($url) {
		$this->wholeView->scripts[] = & $url;
	}

	function addStyleSheet($url) {
		$this->wholeView->style_sheets[] = & $url;
	}

	function renderExtraHeaderContent() {}

	function & view() {
		return $this->wholeView;
	}

	function loadTemplates() {}
	function getId() {
		return "app";
	}

	function getRealId() {
		return "app/" . getClass($this) . "/main";
	}

	function needsView(& $comp) {
		$this->needView[] = & $comp;
	}

	function translate($msg) {
		return $this->translator->translate($msg);
	}
	function launch() {
		if ($_REQUEST["restart"]=="yes") {
			session_destroy();
			session_start();
		}
		if ($_REQUEST["reset"]=="yes") {
			unset($_SESSION[sitename][app_class]);
		}
		$ad =& new ActionDispatcher();
		$app =& $ad->dispatch();
		if (isset($_REQUEST["start"])){
			$app->initialRender();
		} else {
			$app->render();
		}
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
}
?>