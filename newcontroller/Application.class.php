<?php

require_once dirname(__FILE__) . '/Component.class.php';
require_once dirname(__FILE__) . '/ComponentHolder.class.php';
require_once dirname(__FILE__) . '/UrlManager.class.php';
require_once dirname(__FILE__) . '/BackButtonManager.class.php';
require_once dirname(__FILE__) . '/ErrorReporter.class.php';
require_once dirname(__FILE__) . '/ComponentRenderer.class.php';
require_once dirname(__FILE__) . '/../flowviews/html/HTMLRenderer.class.php';

class Application extends ComponentHolder
{
  var $configuration;
  var $wholeView;
  var $viewCreator;
  function Application() {
    parent::ComponentHolder($this->set_root_component(), 0,$this);

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
		                                                       'paged_expired' => 'SimpleErrorReporter'));
    }

    function instantiate_configuration_objects(){
        $url_manager_class = $this->configuration['url_manager'] . 'UrlManager';
        $this->url_manager =& new $url_manager_class($this);
        $backbutton_manager_class = $this->configuration['backbutton_manager'] . 'BackButtonManager';
        $this->backbutton_manager =& new $backbutton_manager_class($this);
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
	function render() {
		$this->component->prepareToRender();
		$v =& $this->wholeView;
		echo $v->renderPage();
		echo $v->childNodes[0]->childNodes[0]->showXML();
	}
	function createView(){
		$this->viewCreator =& new ViewCreator($this); 
		$this->loadTemplates();
		$this->wholeView =& new HTMLRendererNew;
		$this->wholeView->controller =& $this;
		$this->wholeView->append_child($this->component->myContainer());
		$this->viewCreator->createView($this->wholeView, $this->component);
	}
	function &view(){
		return $this->wholeView;  
	}
	function loadTemplates(){}
   	function run() {
	  $this->start();
	  $this->createView();
	  $this->render();
	}
	function getId(){
		return "app";
	}
}

?>
