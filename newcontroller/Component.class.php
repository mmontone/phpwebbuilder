<?php

require_once dirname(__FILE__) . '/actions/FlowAction.class.php';
require_once dirname(__FILE__) . '/PWBObject.class.php';
require_once dirname(__FILE__) . '/Application.class.php';
require_once dirname(__FILE__) . '/ComponentHolder.class.php';

class Component extends PWBObject
{
	var $view;
 	var $model;	
 	var $app;
	var $listener;
	var $holder;
	var $registered_callbacks;
	var $configuration;
	var $__children;
	var $__actions;
	var $__decorators;
	function Component($registered_callbacks=array()) {
		$app =& $this->application();
		$this->registered_callbacks = $registered_callbacks;
		$this->configuration=array('use_component_namemangling' => false,
		                           'use_action_namemangling' => false,
		                           'backtrackable_objects' => array());
		$this->configuration = array_merge($this->configuration, $this->configure());

		$this->__children = array();
		$this->__decorators = array();
		$this->__actions = $this->declare_actions();
		$this->initialize();
	}
	function initialize(){}
	function start() {}
	function configure() {
		return array();
	}
	function &application() {
		return Application::instance();
	}
	function backtrackable_objects() {
		return array();
	}
	function declare_actions() {
	  $this->subclassResponsibility();
	}

	function add_decorator(&$decorator) {
		$this->__decorators[] = $decorator;
    }

	function &copy_for_backtracking() {
		/* PHP4 */
        $my_copy =& parent::copy_for_backtracking();
        $my_copy->listener =& $this->listener->copy_for_backtracking();
        $my_copy->__children =& array_map(create_function('$x', 'return $x->copy_for_backtracking();'), $this->__children);
        return $my_copy;
    }

	function localized($string) {
		return $string;
	}

        function registerCallbacks($callbacks) {
          $this->registered_callbacks =& $callbacks;
        }

	function call_action($action_selector, $params) {
		if (!in_array($action_selector, $this->__actions))
			return false;
		$this->$action_selector($params);
		$this->triggerEvent($action_selector . '_action');
		return true;
	}

	function render_action_link($action_selector,$params=array()) {
        $component_renderer =& ComponentRenderer::getInstance();
        $action =& new FlowAction($this, $action_selector, $params);
        return $component_renderer->render_action_link($action);
    }

    function renderAction($action) {
    	$this->render_action_link($action);
    }

	function add_component(&$component, $index=null) {
		if ($index===null){$index = count($this->__children);}
		$this->__children[$index] =& new ComponentHolder($component,$index, $this);
	}

	function &component_at($index) {
		$holder =& $this->__children[$index];
		return $holder->component;
	}

	function notify($message, $callback_action='notification_accepted') {
          $this->call(new NotificationDialog($message, array('callback' => callback($this, $callback_action))));
	}

	function question($message, $callback_action='question_confirmed') {
          $this->call(new QuestionDialog($message, array('callback' => callback($this, $callback_action))));
	}

	function render_on(&$html) {
	   $this->subclassResponsibility();
	}

	function render() {} /* Fake */

    function renderAll(&$html) {
        foreach ($this->__decorators as $decorator) {
            $decorator->renderAll($html);
        }
        $this->render_on(&$html);
    }

    function renderContent(&$html) {
        $component_renderer =& ComponentRenderer::getInstance();
        $component_renderer->render($this, &$html);
    }

    function call(&$component) {
		/* IMPORTANT TODO: Do a more general callback: don't callback to the listerner always */
        // Give control to $component
		$component->listener =& $this;
        $this->stopAndCall($component);
	}
	function setChild($index, &$component){
		$this->__children[$index]->hold($component);
		$this->$index=&$this->__children[$index]->component;
	}
    function stopAndCall(&$component) {
    	$component->app =& $this->app;
        $component->createView();
		$this->replaceView($component);
    	$this->holder->hold($component);
        $component->start();
    }

	function invalid_callback($callback) {
		$app =& $this->application();
		$app->invalid_callback($callback);
	}
	function callback($callback=null, $parameters=array()) {
		/* IMPORTANT TODO: Do a more general callback: don't callback to the listerner always */
		$v =& $this->view->parent;
		$this->replaceView($this->listener);
		//$v->childNodes=array();
        if ($callback == null) {
			$this->holder->hold($this->listener);
		}
		else {
			if ($this->registered_callbacks[$callback] == null) {
				$this->invalid_callback($callback);
			}
			else {
				$this->holder->hold($this->listener);
				$this->registered_callbacks[$callback]->call($parameters);
			}
		}
	}
	function hasPermission($form){
		$id = $_SESSION[sitename]["id"];
        $permission = $this->permissionNeeded($form);
		if ($permission!=""){
			$role = new Role;
			return $role->userHasPermission($id, $permission);
		} else
			return true;
	}
	function permissionNeeded($form){
		return "";
	}
	function noPermission ($form){ // The user has no permission
		$err= $_SESSION[sitename]["Username"] ." needs ".print_r($this->permissionNeeded($form), TRUE);
		trace($err);
	}
	function setForm($form){}
    function loadView($params) {
      $this->aboutToLoadView($params);
      assert($params['view']);
      $this->view = new $params['view']($params);
      $this->view->controller =& $this;
    }
    function aboutToLoadView(&$params) {}
    
    /**
     * Functions for the new type of views.
     */
    function setView(&$view){
		$this->view =& $view;
		$this->view->controller =& $this;
    }
	function viewUpdated ($params){}
/*	function &createView($viewClass){
		$ks = array_keys($this->__children);
		foreach ($ks as $key){
			$comp =& $this->component_at($key);
			$this->view->append_child($comp->createView($viewClass));
			$v =& $comp->view;
		}
		return $this->view;
	}*/
	function &createView(){
		return new HTMLRendererNew;
		//$this->app->viewCreator->createView($this->parent);
	}
	function replaceView(&$other){
		$p =& $this->view->parent();
		$p->replace_child(
					$this->view, 
					$other->view
			);
	}
	function getId(){
		return $this->holder->getId();
	}
	function getSimpleId(){
		return $this->holder->getSimpleId();
	}
	function prepareToRender(){
		$ks = array_keys($this->__children);
		foreach ($ks as $key){
			$comp =& $this->component_at($key);
			$comp->prepareToRender();
		}
	}
	
}



?>