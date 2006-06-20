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

	function Application() {
		$_SESSION[sitename][getClass($this)] = & $this;
		$rc = & $this->setRootComponent();
		parent :: ComponentHolder($rc, $index = 0, $n = null);
		$rc->linkToApp($this);
		$page_renderer = page_renderer;
		$this->page_renderer = new $page_renderer;
		$this->createView();
		$this->historylistener =& new HistoryListener;

	}
	function setRootComponent(){
		trigger_error("Subclass responsibility");
		exit;
	}
	function start() {
		$this->component->start();
	}

	function redraw() {
		$this->wholeView->flushModifications();
		$this->wholeView->replaceChild($this->wholeView->first_child(), $other = $this->wholeView->first_child());
	}

	function initialRender() {
		$this->viewCreator->createAllViews();
		echo $this->page_renderer->initialPageRenderPage($this);
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

	function render() {
		$this->viewCreator->createAllViews();
		echo $this->page_renderer->renderPage($this);
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

	function run() {
		$this->start();
		$this->initialRender();
	}

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
}
?>