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
	var $toLink = array();
	var $nextChildrenPosition = 0;
	var $dyn_vars = array();

	function Component($params=array()) {
		parent::PWBObject($params);
		$this->__children = array();
		$this->listener =& new ChildCallbackHandler();
	}

	function setDynVar($name, &$value) {
		$this->dyn_vars[$name] =& $value;
	}

	function &getDynVar($name) {
		if (isset($this->dyn_vars[$name])) {
			return $this->dyn_vars[$name];
		}
		else {
			$parent =& $this->getParent();
			#@check $parent !== null@#
			return $parent->getDynVar($name);
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
		 #@check $this->viewHandler!=null@#
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
		#@check !isset($this->app)@#
		$this->app =& $app;
		$this->obtainView();
		$this->initialize();

		$this->start();
		$tl =&$this->toLink;
		#@check is_array($this->toLink)@#
		//if (!is_array($this->toLink)) echo getClass($this)
		foreach(array_keys($tl) as $k){
			$comp =& $tl[$k];
			#@typecheck $comp: Component@#
				$comp->linkToApp($app);
		}
		$null = array();
		$this->toLink =& $null;
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
    	#@typecheck $callback:FunctionObject@#
    	$this->registered_callbacks[$selector] =& $callback;
    }
	function &addComponent(&$component, $ind=null) {
		#@check is_a($component, 'Component')@#
		$res = $component->checkAddingPermissions();
		if ($res == false){
			return $f=false;
		} else {
			if (($ind !==null) and (isset($this->__children[$ind]))) {
				$this->$ind->stopAndCall($component);
			} else {
				#@check !isset($this->$ind)@#
				if ($ind===null){
					$ind = $this->nextChildrenPosition++;
				}
				$this->__children[$ind] =& new ComponentHolder($component,$ind, $this);
				if (isset($this->app)) {
					$component->linkToApp($this->app);
				} else {
					$this->toLink[]=&$component;
				}
			}
			return $component;
		}
	}

	function deleteComponentAt($index){
		$c =& $this->componentAt($index);
		if ($c !== false) $c->delete();
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
		#@typecheck $component:Component@#
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
		#@typecheck $component:Component@#
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
		#@check $this->listener !== null@#
		$this->listener->takeControlOf($this, $callback, $params);
	}

	function takeControlOf(&$callbackComponent, $callback, &$params) {
		#@typecheck $callbackComponent:Component@#
		$n=null;
		$callbackComponent->listener =& $n;
		$callbackComponent->stopAndCall($this);
        if (($callback != null) and ($callbackComponent->registered_callbacks[$callback] != null)) {
			$callbackComponent->registered_callbacks[$callback]->callWith($params);
		}
	}

	function dynCallback($callback=null) {
		$this->dynCallbackWith($callback, $a = array());
	}

	function dynCallbackWith($callback, &$params) {
		#@check $this->listener !== null@#
		$this->listener->dynTakeControlOf($this, $callback, $params);
	}

	function dynTakeControlOf(&$callbackComponent, $callback, &$params) {
		#@typecheck $callbackComponent:Component@#
		$n=null;
		$callbackComponent->listener =& $n;
		$callbackComponent->stopAndCall($this);
        if (($callback != null) and ($callbackComponent->registered_callbacks[$callback] != null)) {
			$callbackComponent->registered_callbacks[$callback]->callWith($params);
		}
		else {
			#@check $this->listener !== null@#
			$this->listener->dynTakeControlOf($this, $callback, $params);
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
		#@typecheck $comp:Component@#
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
}

#@mixin EditorComponent
{
	function addFieldComponent(& $component, $field_name, $text=null) {
		#@typecheck $component:Component@#
		#@check $field_name !== null@#
		if ($text == null) {
			$text = $field_name;
		}
		$fc = & new FieldComponent;
		$fc->addComponent(new Label(Translator :: Translate(ucfirst($text))), 'field_name');
		$fc->addComponent($component, 'component');
		$this->addComponent($fc, $field_name);
	}
}// @#

class FieldComponent extends Component{
	function &getValue(){
		return $this->component->getValue();
	}
	function setValue(&$value){
		$this->component->setValue($value);
	}

}
?>