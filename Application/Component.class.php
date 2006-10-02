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

	function Component($params=array()) {
		parent::PWBObject($params);
		$this->__children = array();
		$this->listener =& new ChildCallbackHandler();
	}
	function initialize(){}
	function start() {}
	function stop() {}
	function stopAndRelease() {
		$this->stopAll();
		$this->releaseAll();
	}

	function stopAll() {
		$this->stop();
		foreach(array_keys($this->__children) as $c) {
			$child =& $this->__children[$c]->component;
			if ($child!=null)$child->stopAll();
		}
		if ($this->listener != null)
			$this->listener->releaseAll();
	}

	function release() {
		parent::release();
		$n = null;
		$this->app =& $n;
	    $this->view =& $n;
		foreach(array_keys($this->__children) as $c) {
			$child =& $this->__children[$c]->component;
			if ($child!=null)$child->release();
		}
	}
	function checkAddingPermissions(){
		return true;
	}
	function releaseAll() {
		$this->release();
		if ($this->listener != null)
			$this->listener->releaseAll();
	}
	function createViews(){
			$this->app->needsView($this);
			$ks = array_keys($this->__children);
			foreach($ks as $k){
				$this->$k->createViews();
			}
	}
	function linkToApp(&$app){
		if (!isset($this->app)){
			$this->app =& $app;
			//print_backtrace();

			$app->needsView($this);
			$this->initialize();
			$ks = array_keys($this->__children);
			foreach($ks as $k){
				if (!is_a($this->__children[$k]->component, 'Component')) print_backtrace($k.' not a component, a '.getClass($this->__children[$k]->component));
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
	function &addComponent(&$component, $ind=null) {
		//echo 'Adding component: ' . getClass($component) . '<br />';
		if (!is_a($component, 'Component')) {
			print_backtrace('Type error adding component: ' . getClass($component));
			trigger_error('Type error adding component: ' . getClass($component),E_USER_ERROR);
		}
		if (!$component->checkAddingPermissions()){
			return $f=false;
		} else {
			if (($ind !=null) and (isset($this->__children[$ind]))) {
				trigger_error('Setting child '.$ind.' from '.$this->getId().' (a '.getClass($component).')',E_USER_NOTICE);
				$this->$ind->stopAndCall($component);
			} else {
				if (isset($this->$ind)) {
					print_backtrace("Replacing variable $ind with component ".getClass($component));
					trigger_error("Replacing variable $ind with component ".getClass($component),E_USER_ERROR);
				}
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
	}

	function deleteComponentAt($index){
		$c =& $this->componentAt($index);
		trigger_error('Removing child '.$index.' from '.$this->getId().' (a '.getClass($c).')',E_USER_NOTICE);
		if ($c != false) $c->delete();
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
		$this->stopAndRelease();
	}
	function redraw(){
		if ($this->view){
			$this->view->redraw();
		}
	}
	function &componentAt($index) {
		if(isset($this->__children[$index])){
			$holder =& $this->__children[$index];
			return $holder->component;
		} else {
			return false;
		}
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
		$this->stopAll();
    	$this->basicCall($component);
    	$this->releaseAll();
    }
    function basicCall(&$component) {
    	//echo 'Calling component: ' . getClass($component) . '<br />';
    	$this->replaceView($component);
    	$this->holder->hold($component);
		if (isset($this->app))$component->linkToApp($this->app);
    }

	function dettachView(){
		$this->view->parentNode->removeChild($this->view);
	}

	function callback($callback=null) {
		$this->callbackWith($callback, $a = array());
	}

	function callbackWith($callback, &$params) {
		if ($this->listener === null) {
			print_backtrace('Component constructor not being called??');
		}
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
		$t =& new HTMLContainer('',array('class'=>'Component'));
		$v->appendChild($t);
		return $v;
	}
	function &createView(&$parentView){
		return $this->app->viewCreator->createView($parentView, $this);
	}
	function replaceView(&$other){
		if (isset($other->view)){
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
	    	$this->holder->parent->view->getTemplatesAndContainers();
	    	//$this->holder->parent->view->addTemplatesAndContainers($a1=array(),$a2=array(),$a3=array(strtolower($cont->attributes['id'])=>&$cont));
	    }
	}
	function &myContainer(){
		$cont =& new HTMLContainer('',array('id'=>$this->getSimpleID()));
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
	function getWidgets(&$ws){
		$ks = array_keys($this->__children);
		foreach ($ks as $key){
			$comp =& $this->componentAt($key);
			$comp->getWidgets(&$ws);
		}
	}
	function &getParent() {
		return $this->holder->parent;
	}
}



?>