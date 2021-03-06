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

		if (defined('dbgmode')) {
			$rc =& new DbgWindow($rc);
		}

		#@typecheck $rc:Component@#
		$c->stopAndCall($rc);
	}

	function addWindow(&$win, $pos){
		$this->windows[$pos]=&$win;
		$win->createView();
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
            Session::setAttribute($class, $app);
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

    function &getParent() {
    	$n = null;
    	return $n;
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
		$this->addScript(constant('pwb_url') . 'lib/scriptaculous/prototype.js');
		$this->addScript(constant('pwb_url') . 'lib/scriptaculous/scriptaculous.js');
		$this->addScript(constant('pwb_url') . 'lib/scriptaculous/Proto.Menu.0.5.js');
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
		$this->addScript(constant('pwb_url') . 'lib/dhtmlHistory.js');
		$this->addScript(constant('pwb_url') . 'lib/ajax.js');
		if (defined('use_animations'))$this->addScript(constant('pwb_url') . 'lib/animatedajax.js');
		$this->addScript(constant('pwb_url') . 'lib/history.js');
	}
	function rendersAjax(){
		$app =& Application::Instance();
		return $app->page_renderer->rendersAjax();
	}
	function preparePage() {
		$this->page_renderer->preparePage();
	}



	function addCometRenderingSpecificScripts() {
		$this->addAjaxRenderingSpecificScripts();
		$this->addScript(constant('pwb_url') . 'lib/comet.js');
	}

	function addXULRenderingSpecificScripts() {
		$this->addScript(constant('pwb_url') . 'lib/ajax.js');
		$this->addScript(constant('pwb_url') . 'lib/comet.js');
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
        $window =& ActionDispatcher::dispatch();
		$window->render();
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
	function releaseSession(){
		$this->page_renderer->releaseSession();
	}
	function closeWindow(){
		$this->windows[$_POST['window']]->close();
	}
	function checkReleaseSession(){
		$this->page_renderer->checkReleaseSession();
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
	function unsetDynVar($name) {
		unset($this->dyn_vars[$name]);
	}
	function issetDynVar($name) {
		return $this->getDynVar($name)!==null;
	}
	function &getDynVar($name) {
		if (isset($this->dyn_vars[$name])) {
			return $this->dyn_vars[$name];
		}
		else {
			$parent =& $this->getParent();
			#@check $parent !== null@#
			if (method_exists($parent,'getDynVar')) {
				return $parent->getDynVar($name);
			} else {
				$n = null;
				return $n;
			}
		}
	}

    function &getVeryDynVar($name) {
        if (isset($this->dyn_vars[$name])) {
            return $this->dyn_vars[$name];
        }
        else {
            /*Search in the listeners, no need to search the parent (it will be searched anyway)*/

            if (method_exists($this->listener,'getVeryDynVar')) {
                return  $this->listener->getVeryDynVar($name);

            }
            /*Search in the parents*/
            $parent =& $this->getParent();
            #@check $parent !== null@#
            if (method_exists($parent,'getVeryDynVar')) {
                return $parent->getVeryDynVar($name);
            } else {
                $n = null;
                return $n;
            }
        }
    }
}//@#

?>