<?php

require_once dirname(__FILE__) . '/ComponentHolder.class.php';

class Application extends ComponentHolder
{
  var $configuration;
  var $wholeView;
  var $viewCreator;
  var $page_renderer;

  function Application() {
  	$n = null;
	$_SESSION['app']['current_app'] =& $this;
  	$rc =& $this->set_root_component();
    parent::ComponentHolder($rc, 0,$n);
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
    }

    function instantiate_configuration_objects(){
        /*$url_manager_class = $this->configuration['url_manager'] . 'UrlManager';
        $this->url_manager =& new $url_manager_class($this);
        $backbutton_manager_class = $this->configuration['backbutton_manager'] . 'BackButtonManager';
        $this->backbutton_manager =& new $backbutton_manager_class($this);*/
        // The following is not true, don't know why :):
        // Interesting case for PHP like references: they are alias, so we can initialize a reference beafore
        // the pointed object has any value. The refernce will be actualized automatically
        $this->page_renderer =& new $this->configuration['page_renderer']($null);
        //$this->page_renderer =& new $this->configuration['page_renderer']($null);
    }

	function configure() {
		return array();
	}

	function start() {
		$this->component->start();
		$this->component->setApp($this);
	}

    function &instance() {
        return $_SESSION['app']['current_app'];
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

	function initialRender() {
		$this->component->prepareToRender();
		$initial_page_renderer =& new StandardPageRenderer($this->wholeView);
		echo $initial_page_renderer->renderPage();
	}

	function render() {
		//$this->wholeView->updateFullPath();
		//$this->component->prepareToRender();
		echo $this->page_renderer->renderPage();
	}

	function createView(){
		if (!$this->viewCreator){
			$this->viewCreator =& new ViewCreator($this);
			$this->loadTemplates();
			$this->wholeView =& new XMLNodeModificationsTracker;
			$this->wholeView->controller =& $this;
			$this->wholeView->append_child($this->component->myContainer());
			$this->wholeView->csss =& $this->setCss();
			$this->page_renderer->page =& $this->wholeView;
		}
	}
	function setCss(){}
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
		return "app/main";
	}
}

?>