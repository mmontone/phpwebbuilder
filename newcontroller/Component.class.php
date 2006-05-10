<?php

require_once dirname(__FILE__) . '/actions/FlowAction.class.php';
require_once dirname(__FILE__) . '/PWBObject.class.php';
require_once dirname(__FILE__) . '/Application.class.php';
require_once dirname(__FILE__) . '/ComponentHolder.class.php';
require_once dirname(__FILE__) . '/Callback.class.php';

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
	var $nextChildrenPosition =0;
	function Component($registered_callbacks=array()) {
		parent::PWBObject();
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
	function linkToApp(&$app){
		$this->app =& $app;
		$ks = array_keys($this->__children);
		$app->needsView($this);
		foreach($ks as $k){
			$this->__children[$k]->component->linkToApp($app);
		}
	}

	function configure() {
		return array();
	}
	function &application() {
		return Application::instance();
	}
	function backtrackable_objects() {
		return array();
	}
	function declare_actions() {}

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

	function add_component(&$component, $ind=null) {
		if ($ind !=null && $this->__children[$ind]!=null) {
			$this->__children[$ind]->component->stopAndCall($component);
		} else {
			$keys = array();
			$index =& $keys[$ind];
			$index = $ind;
			if ($index===null){$index = $this->nextChildrenPosition;}
			if ($this->app!=null) $component->linkToApp($this->app);

			$this->__children[$index] =& new ComponentHolder($component,$index, $this);
			$this->nextChildrenPosition++;
		}
	}
	function delete_component_at($index){
		$c =& $this->component_at($index);
		$c->delete();
	}
	function delete(){
		$pv =& $this->parentView();
		$v =& $this->view;
		$pv->remove_child($v);
		unset($pv);
		unset($v);
		$h =& $this->holder;
		$p =& $h->parentNode;
		$pos =&  $h->__owner_index;
		unset($p->__children[$pos]);
		unset($p->$pos);
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
    	$this->replaceView($component);
    	$this->holder->hold($component);
        $component->start();
    }
	function dettachView(){
		$this->view->parentNode->remove_child($this->view);
	}
	function invalid_callback($callback) {
		$app =& $this->application();
		$app->invalid_callback($callback);
	}
	function callback($callback=null, $parameters=array()) {
		/* IMPORTANT TODO: Do a more general callback: don't callback to the listerner always */
		$this->replaceView($this->listener);
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
      $this->view =& new $params['view']($params);
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
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker;
		$ks = array_keys($this->__children);
		foreach ($ks as $key){
			$v->append_child($this->$key->myContainer());
		}
		return $v;
	}
	function &createView(&$parentView){
		return $this->app->viewCreator->createView($parentView, $this);
	}
	function replaceView(&$other){
    	$other->linkToApp($this->app);
    	$this->createContainer();
	}
	function createContainer(){
    	$cont=& $this->myContainer();
    	$pv =& $this->view->parentNode;
    	$pv->replace_child($cont, $this->view);
	}
	function &myContainer(){
		$cont =& new HTMLContainer;
    	$cont->attributes['id'] = $this->getSimpleID();
    	return $cont;
	}
	function getId(){
		return $this->holder->getRealId();
	}
	function &parentView(){
		return $this->holder->view();
	}
	function getSimpleId(){
		return $this->holder->getSimpleId();
	}
	function prepareToRender(){}
	/* For debugging */
	function printTree(){
		$ks = array_keys($this->__children);
		foreach ($ks as $key){
			$comp =& $this->component_at($key);
			$ret .=  $key ."=>". $comp->printTree()."\n<br/>";
		}
		$ret = str_replace("\n<br/>", "\n<br/>&nbsp;&nbsp;&nbsp;", $ret);
		return $ret;
	}
}



?>