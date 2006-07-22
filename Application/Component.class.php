<?php

class Component extends PWBObject
{
	var $view;
 	var $model;
 	var $app=null;
	var $listener;
	var $holder;
	var $registered_callbacks = array();
	var $configuration;
	var $__children;
	var $nextChildrenPosition =0;

	function Component($registered_callbacks=array()) {
		parent::PWBObject();
		$this->registered_callbacks = $registered_callbacks;
		$this->__children = array();
		$this->listener =& new ChildCallbackHandler();
	}
	function initialize(){}
	function start() {}
	function stop() {
		$n = null;
		$this->app =& $n;
	    $this->view =& $n;
		$this->releaseAll();
	}

	function release() {
		parent::release();
		foreach(array_keys($this->__children) as $c) {
			$child =& $this->__children[$c]->component;
			$child->stop();
		}
	}
	function checkAddingPermissions(){
		return true;
	}
	function releaseAll() {
		$this->release();
		if ($this->listener != null)
			$this->listener->stop();
	}


	function linkToApp(&$app){
		if (!isset($this->app)){
			$this->app =& $app;
			$app->needsView($this);
			$this->initialize();
			$ks = array_keys($this->__children);
			foreach($ks as $k){
				$this->__children[$k]->component->linkToApp($app);
			}
			$this->start();
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
		if (!$component->checkAddingPermissions()) return $f=false;
		if (($ind !=null) and (isset($this->__children[$ind]))) {
			trigger_error('Setting child '.$ind.' from '.$this->getId().' (a '.getClass($component).')',E_USER_NOTICE);
			$this->$ind->stopAndCall($component);
		} else {
			$keys = array();
			$index =& $keys[$ind];
			$index = $ind;
			if ($index===null){$index = $this->nextChildrenPosition;}
			trigger_error('Adding child '.$index.' from '.$this->getId().' (a '.getClass($component).')',E_USER_NOTICE);
			$this->__children[$index] =& new ComponentHolder($component,$index, $this);
			$this->nextChildrenPosition++;
			if (isset($this->app)) $component->linkToApp($this->app);
			$component->start();
		}
		return $component;
	}

	function deleteComponentAt($index){
		$c =& $this->componentAt($index);
		trigger_error('Removing child '.$index.' from '.$this->getId().' (a '.getClass($c).')',E_USER_NOTICE);
		if ($c !== null) $c->delete();
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
			$pv->removeChild($v);
		}
		unset($pv);
		unset($v);
		$h =& $this->holder;
		$p =& $h->parent;
		$pos =&  $h->__owner_index;
		unset($p->__children[$pos]);
		unset($p->$pos);
		$this->stop();
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
	function setChild($index, &$component){
		$this->__children[$index]->hold($component);
		$this->$index=&$this->__children[$index]->component;
	}

	function call(&$component) {
		// Give control to $component
		$component->listener =& $this;
    	$this->basicCall($component);
	}
    function stopAndCall(&$component) {
    	$this->basicCall($component);
		$this->stop();
    }
    function basicCall(&$component) {
    	$this->replaceView($component);
    	$this->holder->hold($component);
		if (isset($this->app))$component->linkToApp($this->app);
        $component->start();
    }

	function dettachView(){
		$this->view->parentNode->removeChild($this->view);
	}

	function callback($callback=null) {
		$this->callbackWith($callback,$a = array());
	}

	function callbackWith($callback, &$params) {
		$this->listener->takeControlOf($this, $callback, $params);
	}

	function takeControlOf(&$callbackComponent, $callback, &$params) {
		$n=null;
		$callbackComponent->listener =& $n;
		$callbackComponent->stopAndCall($this);
        if (($callback != null) and ($callbackComponent->registered_callbacks[$callback] != null)) {
			$callbackComponent->registered_callbacks[$callback]->callWith($params);
		}
	}

	function hasPermission($form){
        $permission = $this->permissionNeeded($form);
		if ($permission!=''){
			return fHasPermission(0, $permission);
		} else
			return true;
	}

	function permissionNeeded(){
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
		$t =& new HTMLContainer;
		$t->setAttribute('class', 'Component');
		$v->appendChild($t);
		/*$ks = array_keys($this->__children);
		foreach ($ks as $key){
			$v->appendChild(
				$this->$key->myContainer()
			);
		}*/
		return $v;
	}
	function &createView(&$parentView){
		return $this->app->viewCreator->createView($parentView, $this);
	}
	function replaceView(&$other){
		if ($other->view){
			$pv =& $this->view->parentNode;
			$pv->replaceChild($other->view, $this->view);
		} else {
    		$this->createContainer();
		}
	}
	function createContainer(){
    	$v =&$this->view;
	    $pv =& $v->parentNode;
    	if ($v!=null && $pv!=null) {
	    	$cont=& $this->myContainer();
	    	$pv->replaceChild($cont, $v);
	    	$this->holder->parent->view->addTemplatesAndContainers($a1=array(),$a2=array(),$a3=array($cont->attributes['id']=>&$cont));
	    }
	}
	function &myContainer(){
		$cont =& new HTMLContainer;
    	$cont->attributes['id'] = $this->getSimpleID();
    	return $cont;
	}
	function getId(){
		if ($this->holder){
			return $this->holder->getRealId();
		} else {
			return '';
		}
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

	function translate($msg) {
		return $this->app->translate($msg);
	}
}



?>