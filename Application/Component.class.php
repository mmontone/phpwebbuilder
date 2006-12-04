<?php

class Component extends PWBObject
{
	var $view;
	var $viewHandler;
 	var $model;
 	var $app=null;
	var $listener;
	var $holder;
	var $registered_callbacks = array();
	var $configuration;
	var $__children;
	var $nextChildrenPosition = 0;
	var $dyn_vars = array();

	function Component($params=array()) {
		parent::PWBObject($params);
		$this->__children = array();
		$this->listener =& new ChildCallbackHandler();
	}

	function setDynVar($name, &$value) {
		//print_backtrace('Setting dyn var at: ' . $name . ' in ' . getClass($this));
		$this->dyn_vars[$name] =& $value;
	}

	function &getDynVar($name) {
		if (isset($this->dyn_vars[$name])) {
			//print_backtrace('Returning dyn var at: ' . $name . ' in ' . getClass($this));
			return $this->dyn_vars[$name];
		}
		else {
			$parent =& $this->getParent();
			if ($parent == null) {
				print_backtrace_and_exit('Dynamic variable ' . $name . ' not defined');
			}
			else {
				//print_backtrace('Dyn var at: ' . $name . ' not found in ' . getClass($this));
				return $parent->getDynVar($name);
			}
		}
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
	}
	function releaseView(){
		 /*@check $this->viewHandler!=null*/
		 $n = null;
		 $this->view =& $n;
		 $this->viewHandler->release();
		 $this->viewHandler =& $n;
		foreach(array_keys($this->__children) as $c) {
			$child =& $this->__children[$c]->component;
			if ($child!=null)$child->releaseView();
		}
	}
	function release() {
		parent::release();
		$n = null;

	    if ($this->viewHandler) $this->viewHandler->release();
	    $this->viewHandler =& $n;
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
		$this->obtainView();
		$ks = array_keys($this->__children);
		foreach($ks as $k){
			$this->$k->createViews();
		}
	}
	function obtainView() {
		$this->app->needsView($this);
	}

	function linkToApp(&$app){
		//@notcheck (!isset($this->app));
		// No se porque es necesaria la siguiente linea bajo las condiciones en que se esta llamando a linkToApp
		// Pero si no esta, falla:
		if ($this->app!==null) return;

		$this->app =& $app;

		$this->obtainView();
		$this->initialize();

		$this->start();
		foreach(array_keys($this->__children) as $k){
			/*@check is_a($this->$k, 'Component')*/
			$this->$k->linkToApp($app);
		}
	}

	function startAll() {
		$this->start();
		foreach(array_keys($this->__children) as $k){
			$this->__children[$k]->component->startAll();
		}
	}

	function &application() {
		return $this->app;
	}

	/**
	 * Receives an array of selector=>FunctionObject
	 */
	function registerCallbacks($callbacks) {
		$this->registered_callbacks =& $callbacks;
    }
	/** Registers a callback for the component */
    function registerCallback($selector, &$callback) {
    	$this->registered_callbacks[$selector] =& $callback;
    }
	function &addComponent(&$component, $ind=null) {
		//echo 'Adding component: ' . getClass($component) . '<br />';
		/*@check is_a($component, 'Component')*/
		$res = $component->checkAddingPermissions();
		if ($res == false){
			return $f=false;
		} else {
			if (($ind !==null) and (isset($this->__children[$ind]))) {
				$this->$ind->stopAndCall($component);
			} else {
				/*@check !isset($this->$ind)*/
				if ($ind===null){
					$ind = $this->nextChildrenPosition++;
				}
				$this->__children[$ind] =& new ComponentHolder($component,$ind, $this);
				if (isset($this->app)) {
					$component->linkToApp($this->app);
				}
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
		if ($this->view->parentNode)$this->view->parentNode->removeChild($this->view);
		$h =& $this->holder;
		$p =& $h->parent;
		$pos =&  $h->__owner_index;
		unset($p->__children[$pos]);
		unset($p->$pos);
		$this->stopAndRelease();
	}
	function redraw(){
		if ($this->viewHandler){
			$this->viewHandler->redraw();
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
		$this->basicCall($component);
    	$this->releaseAll();
    }
    function basicCall(&$component) {
    	//echo 'Calling component: ' . getClass($component) . '<br />';
    	$this->stopAll();
    	$this->replaceView($component);
    	$this->holder->hold($component);
		if (isset($this->app) and (!isset($component->app))) {
			$component->linkToApp($this->app);
		}
		else {
			$component->startAll();
		}
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
		$err= $_SESSION["Username"] ." needs ".print_r($this->permissionNeeded($form), TRUE);
		trace($err);
	}

	/**
     * Functions for the new type of views.
     */
	function viewUpdated ($params){}
	// TODO Remove View
	function replaceView(&$other){
		$other->takeView($this);
	}

	function takeView(&$comp) {
		if (isset($this->view)){
			$pv =& $comp->view->parentNode;
			$pv->replaceChild($this->view, $comp->view);
		} else {
    		$comp->createContainer();
		}
	}
	function createContainer(){
    	$v =&$this->view;
	    $pv =& $v->parentNode;
    	if ($v!=null && $pv!=null) {
	    	$cont=& $this->myContainer();
	    	$pv->replaceChild($cont, $v);
	    	$vp =& $this->parentView();
    	  	$vp->addTemplatesAndContainers($a1=array(),$a2=array(),$a3=array(strtolower($cont->attributes['id'])=>&$cont));
	    	//$this->holder->parent->view->addTemplatesAndContainers($a1=array(),$a2=array(),$a3=array(strtolower($cont->attributes['id'])=>&$cont));
	    }
	}
	function &myContainer(){
		$cont =& new HTMLContainer('',array('id'=>$this->getSimpleID()));
    	return $cont;
	}
	function getId(){
		return $this->holder->getRealId();
	}
	//TODO Remove View
	function &parentView(){
		return $this->holder->view();
	}
	function getSimpleId(){
		return $this->holder->getSimpleId();
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
	function doNothing(){}
	function addFieldComponent(& $component, $field_name, $text=null) {
		/*@check $field_name !== null*/
		if ($text == null) {
			$text = $field_name;
		}
		$fc = & new FieldComponent;
		$fc->addComponent(new Label(Translator :: Translate(ucfirst($text))), 'field_name');
		$fc->addComponent($component, 'component');
		$this->addComponent($fc, $field_name);
	}
}

class FieldComponent extends Component{
	function &getValue(){
		return $this->component->getValue();
	}
	function setValue(&$value){
		$this->component->setValue($value);
	}

}
?>