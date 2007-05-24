<?php
class Component extends PWBObject {
	var $view;
	var $viewHandler;
	var $model;
	var $app = null;
	var $listener;
	var $holder;
	var $registered_callbacks = array ();
	var $configuration;
	var $__children;
	var $toLink = array ();
	var $nextChildrenPosition = 1;
	var $dyn_vars = array ();

	function Component($params = array ()) {
		$this->componentstates =& new Collection;
		parent :: PWBObject($params);
		$this->__children = array ();
		$this->listener = & new ChildCallbackHandler();
	}
	#@use_mixin DynVars@#
	function initialize() {
	}
	function start() {
	}

	function stop() {
	}

	function stopAll() {
		#@activation_echo echo 'Stopping ' . $this->printString() . '<br/>';@#
        foreach (array_keys($this->__children) as $c) {
			$child = & $this->__children[$c]->component;
			if ($child !== null) {
				$child->stopAll();
			}
		}
        $this->stop();
	}

	function stopAndRelease() {
		$this->stopAll();
		$this->releaseAll();
	}

	function reloadView() {
		#@check $this->viewHandler!=null@#
		$n = null;
		$this->view = & $n;
		$this->viewHandler->release();
		$this->viewHandler = & $n;
		$this->obtainView();
		foreach (array_keys($this->__children) as $c) {
			$child = & $this->__children[$c]->component;
			if ($child != null) {
				$child->reloadView();
			}
		}
	}
	function & getWindow() {
		return $this->getDynVar('window');
	}
	function releaseView() {
		#@check $this->viewHandler!=null@#
		$n = null;
		$this->view = & $n;
		$this->viewHandler->release();
		$this->viewHandler = & $n;
		foreach (array_keys($this->__children) as $c) {
			$child = & $this->__children[$c]->component;
			if ($child != null)
				$child->releaseView();
		}
	}
	function release() {
		parent :: release();
		$n = null;

		if ($this->viewHandler)
			$this->viewHandler->release();
		$this->viewHandler = & $n;
		foreach (array_keys($this->__children) as $c) {
			$child = & $this->__children[$c]->component;
			if ($child != null)
				$child->release();
		}
	}
	function checkAddingPermissions() {
		return true;
	}
	function releaseAll() {
		$this->release();
		if ($this->listener != null)
			$this->listener->releaseAll();
	}
	function createViews() {
		$this->obtainView();
		$ks = array_keys($this->__children);
		foreach ($ks as $k) {
			$this-> $k->createViews();
		}
	}
	function obtainView() {
		$this->app->needsView($this);
	}

	function linkToApp(& $app) {
		#@check !isset($this->app)@#
		$this->app = & $app;
		$this->obtainView();
		$this->initialize();

		#@activation_echo 'Starting ' . $this->printString() . '<br/>';@#
        $this->start();
		$tl = & $this->toLink;
		#@check is_array($this->toLink)@#
		//if (!is_array($this->toLink)) echo getClass($this)
		foreach (array_keys($tl) as $k) {
			#@typecheck $tl[$k]: Component@#
			$tl[$k]->linkToApp($app);
		}
		$null = array ();
		$this->toLink = & $null;
	}
	function startAll() {
		#@activation_echo echo 'Starting ' . $this->printString() . '<br/>';@#
        $this->start();
		foreach (array_keys($this->__children) as $k) {
			$this->__children[$k]->component->startAll();
		}
	}

	function & application() {
		return $this->app;
	}

	/**
	 * Receives an array of selector=>FunctionObject
	 */
	function registerCallbacks($callbacks) {
		$this->registered_callbacks = & $callbacks;
	}
	/** Registers a callback for the component */
	function registerCallback($selector, & $callback) {
		#@typecheck $selector:string, $callback:FunctionObject@#
		$this->registered_callbacks[$selector] = & WeakFunctionObject :: fromFunctionObject($callback);
	}
	function & addComponent(& $component, $ind = null) {
		#@check is_a($component, 'Component')@#
		$res = $component->checkAddingPermissions();
		if ($res == false) {
			return $res;
		} else {
			if (($ind !== null) and (isset ($this->__children[$ind]))) {
				$this-> $ind->stopAndCall($component);
			} else {
				#@gencheck if (isset($this->$ind)) {print_backtrace("There is a ".getClass($this->$ind)." in $ind on a ".getClass($this));} else {}@#
				if ($ind === null) {
					$ind = $this->nextChildrenPosition++;
				}
				$this->__children[$ind] = & new ComponentHolder($component, $ind, $this);
				if (isset ($this->app)) {
					$component->linkToApp($this->app);
				} else {
					$this->toLink[] = & $component;
				}
			}
			return $component;
		}
	}

	function deleteComponentAt($index) {
		$c = & $this->componentAt($index);
		if ($c !== false)
			$c->delete();
	}

	function deleteChildren() {
		$ks = array_keys($this->__children);
		foreach ($ks as $k) {
			$this->deleteComponentAt($k);
		}
	}
	function delete() {
		if ($this->view->parentNode)
			$this->view->parentNode->removeChild($this->view);
		$h = & $this->holder;
		$p = & $h->parent;
		$pos = & $h->__owner_index;
		unset ($p->__children[$pos]);
		unset ($p-> $pos);
		$this->stopAndRelease();
	}
	function redraw() {
		if ($this->viewHandler) {
			$this->viewHandler->redraw();
		}
	}
	function & componentAt($index) {
		if (isset ($this->__children[$index])) {
			$holder = & $this->__children[$index];
			return $holder->component;
		} else {
			$false = false;
			return $false;
		}
	}
	function setChild($index, & $component) {
		#@typecheck $component:Component@#
		$this->__children[$index]->hold($component);
		$this-> $index = & $this->__children[$index]->component;
	}

	function call(& $component) {
		// Give control to $component
		#@calling_echo echo $this->printString() . ' calling ' . $component->printString() . '<br/>';@#
        $component->listener = & $this;
		$this->basicCall($component);
	}

	function stopAndCall(& $component) {
		$this->basicCall($component);
		$this->releaseAll();
	}

	function basicCall(& $component) {
		#@typecheck $component:Component@#
		$this->stopAll();
		$this->replaceView($component);
		$this->holder->hold($component);
		if (isset ($this->app) and (!isset ($component->app))) {
			$component->linkToApp($this->app);
		} else {
			$component->startAll();
		}
	}
	function callback($callback = null) {
		$this->callbackWith($callback, $a = array ());
	}

	function callbackWith($callback, & $params) {
		#@check $this->listener !== null@#
		#@calling_echo echo $this->printString() . ' calling back: ' . $callback . '<br/>';@#
        $this->listener->takeControlOf($this, $callback, $params);
	}

	function takeControlOf(& $callbackComponent, $callback, & $params) {
		#@typecheck $callbackComponent:Component@#
		#@calling_echo echo $this->printString() . ' taking control of: ' . $callbackComponent->printString() . ' callback: ' . $callback . '<br/>';@#
        $n = null;
		$callbackComponent->listener = & $n;
		$callbackComponent->stopAndCall($this);
		if (($callback != null) and (isset ($callbackComponent->registered_callbacks[$callback]))) {
			#@calling_echo echo $this->printString() . ' executing callback: ' . $callback . ' function: ' . $callbackComponent->registered_callbacks[$callback]->printString() .'<br/>';@#
            $callbackComponent->registered_callbacks[$callback]->executeWith($params);
		}
	}

	function dynCallback($callback = null) {
		$this->dynCallbackWith($callback, $a = array ());
	}

	function dynCallbackWith($callback, & $params) {
		#@check $this->listener !== null@#
		$this->listener->dynTakeControlOf($this, $callback, $params);
	}

	function dynTakeControlOf(& $callbackComponent, $callback, & $params) {
		#@typecheck $callbackComponent:Component@#
		$n = null;
		$callbackComponent->listener = & $n;
		$callbackComponent->stopAndCall($this);
		if (($callback != null) and ($callbackComponent->registered_callbacks[$callback] != null)) {
			$callbackComponent->registered_callbacks[$callback]->executeWith($params);
		} else {
			#@check $this->listener !== null@#
			$this->listener->dynTakeControlOf($this, $callback, $params);
		}
	}

	function hasPermission($form) {
		$permission = $this->permissionNeeded($form);
		if ($permission != '') {
			return fHasPermission(0, $permission);
		} else
			return true;
	}

	function permissionNeeded() {
		return '';
	}

	function noPermission($form) { // The user has no permission
		$err = Session :: getAttribute("Username") . " needs " . print_r($this->permissionNeeded($form), TRUE);
		trace($err);
	}

	/**
	 * Functions for the views.
	 */
	function viewUpdated($params) {
	}
	// TODO Remove View
	function replaceView(& $other) {

		$other->takeView($this);
	}

	function takeView(& $comp) {
		#@typecheck $comp:Component@#
		if (isset ($this->view)) {
			$pv = & $comp->view->parentNode;
			$pv->replaceChild($this->view, $comp->view);
		} else {
			$comp->createContainer();
		}
	}
	function createContainer() {
		$v = & $this->view;
		$pv = & $v->parentNode;
		if ($v != null && $pv != null) {
			$cont = & $this->myContainer();
			$pv->replaceChild($cont, $v);
			$vp = & $this->parentView();
			$a1 = array ();
			$a2 = array ();
			$a3 = array (
				strtolower($cont->attributes['id']
			) => & $cont);
			$vp->addTemplatesAndContainers($a1, $a2, $a3);
			//$this->holder->parent->view->addTemplatesAndContainers($a1=array(),$a2=array(),$a3=array(strtolower($cont->attributes['id'])=>&$cont));
		}
	}
	function & myContainer() {
		$cont = & new HTMLContainer('', array (
		'id' => $this->getSimpleID()));
		return $cont;
	}
	function getId() {
		return $this->holder->getRealId();
	}
	//TODO Remove View
	function & parentView() {
		return $this->holder->view();
	}
	function getSimpleId() {
		return $this->holder->getSimpleId();
	}
	function translate($msg) {
		return $this->app->translate($msg);
	}
	function getWidgets(& $ws) {
		$ks = array_keys($this->__children);
		foreach ($ks as $key) {
			$comp = & $this->componentAt($key);
			$comp->getWidgets($ws);
		}
	}
	function & getParent() {
		return $this->holder->getParentElement();
	}
	function doNothing() {
	}
	function printString() {
		if (is_object($this->holder)) {
			$id = $this->getId();
		} else {
			$id = 'without id';
		}
		return $this->primPrintString($id);
	}

	function setComponentState($st, $b=true){
		if ($b){$this->componentstates->atPut($st,$st);}
		else{$this->componentstates->remove($st);}
	}

}

#@mixin EditorComponent
{
	function addFieldComponent(& $component, $field_name, $text = null) {
		#@typecheck $component:Component, $field_name:string@#
		if ($text == null) {
			$text = $field_name;
		}
		$fc = & new FieldComponent;
		$fc->addComponent(new Label(Translator :: Translate(ucfirst($text))), 'field_name');
		$fc->addComponent($component, 'component');
		$this->addComponent($fc, $field_name);
	}
} //@#

class FieldComponent extends Component {
	function getValue() {
		return $this->component->getValue();
	}
	function setValue(& $value) {
		$this->component->setValue($value);
	}
}
?>