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
	var $nextChildrenPosition =0;

	function Component($registered_callbacks=array()) {
		parent::PWBObject();
		$this->registered_callbacks = $registered_callbacks;
		$this->__children = array();
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

	function &application() {
		return $this->app;
	}

	function registerCallbacks($callbacks) {
		$this->registered_callbacks =& $callbacks;
    }

    function registerCallback($selector, &$callback) {
    	$this->registered_callbacks[$selector] =& $callback;
    }

	function renderAction($action) {
    	$this->render_action_link($action);
    }

	function &addComponent(&$component, $ind=null) {
		if ($ind !=null && isset($this->__children[$ind])) {
			$this->__children[$ind]->component->stopAndCall($component);
		} else {
			$keys = array();
			$index =& $keys[$ind];
			$index = $ind;
			if ($index===null){$index = $this->nextChildrenPosition;}
			if ($this->app!=null) $component->linkToApp($this->app);
			$this->__children[$index] =& new ComponentHolder($component,$index, $this);
			$component->listener =& new ChildCallbackHandler();
			$this->nextChildrenPosition++;
		}
		return $component;
	}

	function deleteComponentAt($index){
		$c =& $this->componentAt($index);
		$c->delete();
	}

	function deleteChildren(){
		$ks = array_keys($this->__children);
		foreach($ks as $k){
			$this->deleteComponentAt($k);
		}
	}

	function delete(){
		$v =& $this->view;
		$pv =& $v->parentNode;
		if ($v!=null && $pv!=null){
			$pv->remove_child($v);
		}
		unset($pv);
		unset($v);
		$h =& $this->holder;
		$p =& $h->parent;
		$pos =&  $h->__owner_index;
		unset($p->__children[$pos]);
		unset($p->$pos);
		unset($p->__children[$pos]);
		unset($p->$pos);
	}
	function redraw(){
		if ($this->view){
			$this->view->redraw();
		}
	}
	function &componentAt($index) {
		$holder =& $this->__children[$index];
		return $holder->component;
	}

	function call(&$component) {
		// Give control to $component
		$component->listener =& $this;
        $this->stopAndCall($component);
	}

	function setChild($index, &$component){
		$this->__children[$index]->hold($component);
		$this->$index=&$this->__children[$index]->component;
	}

    function stopAndCall(&$component) {
		if ($this->app!=null) $component->linkToApp($this->app);
    	$this->replaceView($component);
    	$this->holder->hold($component);
        $component->start();
    }

	function dettachView(){
		$this->view->parentNode->remove_child($this->view);
	}

	function callback($callback=null) {
		$this->callbackWith($callback,$a = array());
	}

	function callbackWith($callback=null, &$params) {
		if ($this->listener){
			$this->listener->takeControlOf($this, $callback, $params);
		}
	}

	function takeControlOf(&$callbackComponent, $callback=null, &$params) {
		$callbackComponent->replaceView($this);
		$callbackComponent->app->needsView($this);
        if ($callback == null) {
			$callbackComponent->holder->hold($this);
		}
		else {
			if ($callbackComponent->registered_callbacks[$callback] != null) {
				$callbackComponent->holder->hold($this);
				$callbackComponent->registered_callbacks[$callback]->callWith($params);
			}
		}
	}

	function hasPermission($form){
        $permission = $this->permissionNeeded($form);
		if ($permission!=''){
			return fHasPermission($id, $permission);
		} else
			return true;
	}

	function permissionNeeded($form){
		return '';
	}

	function noPermission ($form){ // The user has no permission
		$err= $_SESSION[sitename]["Username"] ." needs ".print_r($this->permissionNeeded($form), TRUE);
		trace($err);
	}

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
			$v->append_child(
				$this->$key->myContainer()
			);
		}
		return $v;
	}
	function &createView(&$parentView){
		return $this->app->viewCreator->createView($parentView, $this);
	}
	function replaceView(&$other){
    	$this->createContainer();
	}
	function createContainer(){
    	$v =&$this->view;
	    $pv =& $v->parentNode;
    	if ($v!=null && $pv!=null) {
	    	$cont=& $this->myContainer();
	    	$pv->replace_child($cont, $v);
	    	$this->holder->parent->view->getTemplatesAndContainers();
	    }
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
			$comp =& $this->componentAt($key);
			$ret .=  $key ."=>". $comp->printTree()."\n<br/>";
		}
		$ret = str_replace("\n<br/>", "\n<br/>&nbsp;&nbsp;&nbsp;", $ret);
		return $ret;
	}
}



?>