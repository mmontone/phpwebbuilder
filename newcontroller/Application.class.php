<?php

require_once dirname(__FILE__) . '/ComponentHolder.class.php';

class Application extends ComponentHolder
{
  var $configuration;
  var $wholeView;
  var $viewCreator;
  var $page_renderer;
  var $needView = array();
  function Application() {
	$_SESSION[sitename][get_class($this)] =& $this;
  	$rc =& $this->set_root_component();
    parent::ComponentHolder($rc, $index=0,$n = null);
    $rc->linkToApp($this);
    $this->set_default_configuration();
    $this->configuration = array_merge((array)$this->configuration, (array)$this->configure());
    $this->instantiate_configuration_objects();
  }

    function set_default_configuration() {
    	$this->configuration = array('url_manager' => 'Ugly',
		                             'db' => array('driver' => 'mysql',
		                                           'name' => 'dbname',
		                                           'host' => 'localhost',
		                                           'username' => 'username',
		                                           'password' => 'userpass'),
		                             'backbutton_manager' => 'DontCare',
		                             'error_reporter' => array('invalid_action' => 'InvalidActionReporter',
		                                                       'component_not_found' => 'InvalidComponentReporter',
		                                                       'invalid_application' => 'SimpleErrorReporter',
		                                                       'paged_expired' => 'SimpleErrorReporter'),
		                             //'page_renderer' => 'AjaxPageRenderer');
		                             'page_renderer' => 'StandardPageRenderer');
		                             //'page_renderer' => 'DebugPageRenderer');
		                             //'page_renderer' => 'DebugAjaxPageRenderer');
    }

    function instantiate_configuration_objects(){
        /*$url_manager_class = $this->configuration['url_manager'] . 'UrlManager';
        $this->url_manager =& new $url_manager_class($this);
        $backbutton_manager_class = $this->configuration['backbutton_manager'] . 'BackButtonManager';
        $this->backbutton_manager =& new $backbutton_manager_class($this);*/
        // The following is not true, don't know why :):
        // Interesting case for PHP like references: they are alias, so we can initialize a reference beafore
        // the pointed object has any value. The refernce will be actualized automatically
        $this->page_renderer =& new $this->configuration['page_renderer']($null=null);
        //$this->page_renderer =& new $this->configuration['page_renderer']($null);
    }

	function configure() {
		return array();
	}

	function start() {
		$this->component->start();
	}
	function &copy_for_backtracking() {
        /* PHP4 */
		/*$app_copy = $this;
        $app_copy->component =& $this->component->copy_for_backtracking();
        return $app_copy;*/
        $component_copy =& unserialize(serialize($this->component));
        $app_copy = $this;
        $app_copy->component =& $component_copy;
        return $app_copy;
	}

	function notify_changes() {
		$this->backbutton_manager->application_changed();
	}

	function invalid_callback($callback) {
		echo "<html><head></head><body>";
		echo "<h1>There's no handler defined for callback: " . $callback . "<h1>";
		echo "</body></html>";
		die;
	}

	function report_error($error_id, $params=array()) {
		$error_reporter_class = $this->configuration['error_reporter'][$error_id];
		$error_reporter =& new $error_reporter_class;
		$error_reporter->report($params);

	}

	function render_action_link(&$action) {
		return $this->url_manager->render_action_link($action);
	}
	function redraw(){
		$this->wholeView->flushModifications();
		$this->wholeView->replace_child($this->wholeView->first_child(),$other = $this->wholeView->first_child());
	}
	function initialRender() {
		$this->viewCreator->createAllViews();
		$initial_page_renderer =& new StandardPageRenderer($this->wholeView);
		//$initial_page_renderer =& new DebugPageRenderer($this->wholeView);
		echo $initial_page_renderer->renderPage();
	}
	function &getInstanceOf($class){
		if (!isset($_SESSION[sitename][$class])){
			$_SESSION[sitename][$class] =& new $class;
		}
		return $_SESSION[sitename][$class];
	}
	function render() {
		$this->viewCreator->createAllViews();
		echo $this->page_renderer->renderPage();
	}
	function createView(){
		if (!$this->viewCreator){
			$this->viewCreator =& new ViewCreator($this);
			$this->loadTemplates();
			$this->wholeView =& new XMLNodeModificationsTracker;
			$this->wholeView->controller =& $this;
			$this->wholeView->append_child($this->component->myContainer());
			$this->wholeView->getTemplatesAndContainers();
			$this->initializeStyleSheets();
			$this->initializeScripts();
			$this->page_renderer->setPage($this->wholeView);
		}
	}
	function initializeStyleSheets() {
		$this->wholeView->style_sheets = array();
		$this->addStyleSheets();
	}

	function initializeScripts() {
		$this->wholeView->scripts = array();
		$this->addScript(pwb_url . '/common.js');
		$this->page_renderer->initializeScripts($this);
		$this->addScripts();
	}

	function addScripts(){}
	function addStyleSheets(){}

	function addScript($url) {
		$this->wholeView->scripts[] =& $url;
	}

	function addStyleSheet($url) {
		$this->wholeView->style_sheets[] =& $url;
	}

	function &view(){
		return $this->wholeView;
	}
	function loadTemplates(){}

   	function run() {
	  $this->start();
	  $this->createView();
	  $this->initialRender();
	}
	function getId(){
		return "app";
	}
	function getRealId(){
		return "app/".get_class($this)."/main";
	}
	function needsView(&$comp){
		$this->needView[]=&$comp;
	}
}

?>