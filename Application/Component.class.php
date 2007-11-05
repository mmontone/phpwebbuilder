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
	var $calling=false; // It is true when the component is calling other component (but not calling back)
    var $calling_back=false; // It is true when then component is calling back
	var $__contextMenus=array();
    var $memory_transaction;

	function Component($params = array()) {
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

	function stopAll($isCaller=true) {
		#@activation_echo echo 'Stopping ' . $this->printString() . '<br/>';@#
        defdyn('current_component', $this);
		foreach (array_keys($this->__children) as $c) {
			$child = & $this->__children[$c]->component;
			if ($child !== null) {
				$child->stopAll(false);
			}
		}

        if ($this->isCalling()) {
        	$this->metaCallStop($isCaller);
        }
        else {
        	$this->metaNonCallStop();
        }
        $this->stop();
        if (!$this->calling_back and !$this->calling) {
        	$this->informStopToCallers($this);
        }
	undefdyn('current_component');
	}

	function informStopToCallers(&$callee) {
		if (is_object($this->listener)) {
			$this->listener->calleeStopped($callee);
			$this->listener->informStopToCallers($callee);
		}
	}
	function isCalling(){
		$p =& $this->getParent();
		return $this->calling || $p->isCalling();

	}
	/* This method is called when a callee component is stopped (not for calling back)
	 * This method is useful to handle memory transactions or long db transactions.
	 * Example (memory transactions):
	 * function nonCallStop() {
	 *     $this->rollbackMemoryTransaction();
	 * }
     *
	 * function calleeStopped(&$dialog) {
	 *     $this->rollbackMemoryTransaction();
 	 * }
 	 * Example (long db transactions):
	 * function nonCallStop() {
	 *     DBSessionInstance::RollbackTransaction();
	 * }
     *
	 * function calleeStopped(&$dialog) {
	 *     DBSessionInstance::RollbackTransaction();
 	 * }
	 */
	function calleeStopped(&$callee) {

	}

	// This function gets called when the component stops because
	// its calling other component. Its useful for some protocols. For example,
	// object editors dont want to flush the changes when they stop for making a call (See ObjectEditor class).
	//                                                    -- marian
	function callStop($isCaller) {

	}
	function metaCallStop($isCaller) {
		if ((!$isCaller) && $this->memory_transaction) $this->memory_transaction->pause();
		$this->callStop($isCaller);
	}
	function metaStart() {
		if ($this->memory_transaction) $this->memory_transaction->restart();
		$this->start();
	}

	// This function gets called when the component stops and it is NOT
	// calling other component. Its useful for some protocols. For example,
	// object editors dont want to flush the changes when they stop (See ObjectEditor class)
	//                                               -- marian
	function metaNonCallStop() {
		if ($this->memory_transaction) $this->memory_transaction->cancel();
		$this->nonCallStop();
	}
	function nonCallStop() {

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
			$this->$k->createViews();
		}
	}
	function obtainView() {
		$this->app->needsView($this);
	}

	function linkToApp(& $app) {
		#@check !isset($this->app)@#
		$this->app = & $app;
		$this->obtainView();
		#@tm_echo2 echo 'Setting current component: ' . $this->debugPrintString() . '<br/>';@#
        defdyn('current_component', $this);
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
		undefdyn('current_component');
		#@tm_echo2 echo 'Unsetting current component: ' . $this->debugPrintString() . '<br/>';@#
	}
	function startAll() {
		#@activation_echo echo 'Starting ' . $this->printString() . '<br/>';@#
        $this->metaStart();
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
	    $this->registered_callbacks[$selector] = & $callback->weakVersion();
	}
	function & addComponent(& $component, $ind = null) {
		#@check is_a($component, 'Component')@#
		$res = $component->checkAddingPermissions();
		if ($res == false) {
			return $res;
		} else {
			if (($ind !== null) and (isset ($this->__children[$ind]))) {
				$this->$ind->stopAndCall($component);
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
		unset ($p->$pos);
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
		$this->$index = & $this->__children[$index]->component;
	}

	function call(& $component) {
		// Give control to $component
		#@calling_echo echo $this->printString() . ' calling ' . $component->printString() . '<br/>';@#
        $this->calling = true;
        $component->listener = & $this;
		$this->basicCall($component);
		$this->calling = false;
	}
	function canRelease(){
		$can = (!is_object($this->memory_transaction)) ||
			$this->memory_transaction->isEmpty();
		foreach (array_keys($this->__children) as $c) {
			$child = & $this->__children[$c]->component;
			if ($child != null)
				$can = $can && $child->canRelease();
		}
		return $can;
	}
	function stopAndCall(& $component) {
		if ($this->canRelease()){
			$this->basicCall($component);
			$this->releaseAll();
			return true;
		} else {
			$this->cantReleaseActions();
			return false;
		}
	}
	function cantReleaseActions(){
		$this->call($ed =& ErrorDialog::create(Translator::Translate('A transaction is active, please close it before exiting')));
		$ed->onAccept(new FunctionObject($this, 'doNothing'));
	}

	function basicCall(& $component) {
		#@typecheck $component:Component@#
		if (isset($this->holder) && !$this->holder->holds($this)){
			//echo '<br/>'.$this->printString().' has next comp '.$this->listener->printString();
			$this->enqueuedCall =& $component;
		} else {
			$this->stopAll();
			$this->replaceView($component);
			$this->holder->hold($component);
			if (isset ($this->app) and (!isset ($component->app))) {
				$component->linkToApp($this->app);
			} else {
				$component->startAll();
			}
		}
	}
	function callback($callback = null) {
		return $this->callbackWith($callback, $a = array ());
	}

	function callbackWith($callback, & $params) {
		#@check $this->listener !== null@#
		#@calling_echo echo $this->printString() . ' calling back: ' . $callback . '<br/>';@#
        $this->listener->calling_back = true;
        $res = $this->listener->takeControlOf($this, $callback, $params);
        $this->listener->calling_back = false;
        return $res;
	}

	function takeControlOf(& $callbackComponent, $callback, & $params) {
		#@typecheck $callbackComponent:Component@#
		#@calling_echo echo $this->printString() . ' taking control of: ' . $callbackComponent->printString() . ' callback: ' . $callback . '<br/>';@#
        $n = null;
		$callbackComponent->listener = & $n;
		//Check if there was an enqueued call
		if (!$callbackComponent->stopAndCall($this)) {
			//Restore the listener of the callback, because it was not possible to remove yet.
			$callbackComponent->listener = & $this;
			return false;
		}
		/*if (isset($this->enqueuedCall)){
			echo 'calling from queue '.$this->enqueuedCall->printString();
			$this->call($this->enqueuedCall);
			unset($this->enqueuedCall);
		}*/
		if (($callback != null) and (isset ($callbackComponent->registered_callbacks[$callback]))) {
			#@calling_echo echo $this->printString() . ' executing callback: ' . $callback . ' function: ' . $callbackComponent->registered_callbacks[$callback]->printString() .'<br/>';@#
            $callbackComponent->registered_callbacks[$callback]->executeWith($params);
		}
		return true;
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
		if (!$callbackComponent->stopAndCall($this)) return;
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
			$pv =& $comp->view->parentNode;
			if (!$pv) ($comp->printString() . $this->printString());
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
	function getLiveId() {
		if ( $this->holder) {
			return $this->holder->getRealId();
		} else{
			return '';
		}
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
	function debugprintString() {
		return $this->printString();
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
	function createContextMenu(){
		$win =& Window::getActiveInstance();
		$win->addAjaxCommand(new AjaxCommand("contextMenus['{$this->getId()}'] = new Proto.Menu({selector: '#{$this->getId()}',className: 'menu',menuItems: []});",array()));
	}
	function destroyContextMenu(){

	}
	function addContextMenu($name, &$functionObject){
		if (empty($this->__contextMenus)) {$this->createContextMenu();}
		$this->__contextMenus[$name] =&$functionObject;
		$win =& Window::getActiveInstance();
		$win->addAjaxCommand(new AjaxCommand("contextMenus['{$this->getId()}'].addElement({title:'$name', callback:function(){alert('$name')}});"));
	}
	function removeContextMenu($name){
		unset($this->__contextMenus[$name]);
		if (empty($this->__contextMenus)) {$this->removeContextMenu();}
	}

    // mixin DBComponent
    // DBComponents implement memory transactions
    // The problem with dynamically-bound memory transactions are:
    // 1) How to get noticed of model changes to record field modifications?: when we are modifying
    //    an object, the memory transaction is not at hand (it's not on the stack). We need real dynamic-variable
    //    support for that.
    // 2) Transactions interleaving: In transaction A, the field O>>f gets modified. So A records old value for the O>>f field.
    //                            Then, the same field gets modified but in transaction B. So B records the value assigned by A as old.
    //                            A rollsback. B rollsback and O>>f value is the value assigned by A and not the original value!!
    //                            Possible solution?: versioning at the fields granularity. An exception is raised whenever a versioning
    //                            error ocurs.

    var $transaction; // The transaction the component may begin

    function beginMemoryTransaction() {
        if (!is_object($this->memory_transaction)) {
            $this->memory_transaction =& new MemoryTransaction($this);
            $this->setDynVar('memory_transaction', $this->memory_transaction);
        }
        #@tm_echo echo 'Beggining memory transaction:' . $this->memory_transaction->debugPrintString() . '<br/>';@#
        $this->memory_transaction->start();
        return $this->memory_transaction;
    }

    function &getMemoryTransaction() {
    	return $this->getVeryDynVar('memory_transaction');
    }

    function rollbackMemoryTransaction() {
        if (!is_object($this->memory_transaction)) {
        	print_backtrace_and_exit('Error: you are trying to rollback a non existing transaction in ' . $this->debugPrintString() . '.' .
                    ' Make sure that beginMemoryTransaction is called before in this component');
        }
        $this->memory_transaction->rollback();
    }

    function commitMemoryTransaction() {
      if (!is_object($this->memory_transaction)) {
		print_backtrace_and_exit('Trying to commit a non started transaction in ' . $this->debugPrintString());
      }
      $this->memory_transaction->commit();
    }

    function commitAndBeginMemoryTransaction() {
    	$this->commitMemoryTransaction();
    	$this->beginMemoryTransaction();
    }

    function saveMemoryTransactionObjects() {
      $transaction =& $this->getMemoryTransaction();
      if (!is_object($transaction)) {
		/* This method is called from CollectionField in order to have observable collections all the time.
		 * It is not clear to me what we should do when there's not an active memory transaction to save the objects.
		 * Options:
		 * a) Do nothing
		 * b) Raise an error
		 * c) Commit objects globally (these memory transactions are not local anyway)
		 * Here we implement c)
		 */
		MemoryTransaction::saveObjectsInTransaction();
      }
      else {
      	$transaction->saveObjectsInTransaction();
      }
    }

    function unregisterAllMemoryTransactionObjects() {
      if (!is_object($this->memory_transaction)) {
	print_backtrace_and_exit('Trying to unregister objects of a non started transaction in ' . $this->debugPrintString());
      }
      $this->memory_transaction->unregisterAllObjects();
    }

    function registerFieldModification(&$mod) {
        $t =& $this->getMemoryTransaction();
        if(!is_object($t)) {
        	/* What should we do if we don't find a memory transaction?
        	 * Options:
        	 * 1) Create a new one in the current component implicitly (the programmer didn't tell to do that)
        	 * Problems with this one: as a memory transaction means a db-transaction or an increment in the transaction nesting (this is
        	 * because we want observable collections and so on) and the programmer was not given a chance to commit or rollback it (he is probably
        	 * not aware of it from code), then we have a memory inconsistency issue or, at least, a transaction nesting inconsistency issue.
        	 * So, that's not an option if we want to have observable queries all the time.
        	 * 2) Avoid registering the object modification in any transaction. We should warn the developer about that using sql_echo, though.
        	 * 3) Raise and error and force the programmer to begin a transaction before the object modification happens.
        	 *
        	 * I'm implementing option 2 (with a hack included), but I think option 3 would be the right way to go.
        	 * The hack is we notify the DBSession of the change anyway (note that these breaks the notion of locality transactions should provide).
        	 * I don't implement option 3 for the moment because it turned out to be to rigid for the application programmer (for instance, all objects modifications
        	 * had to be done in the context of a transaction). Implementing only 2, the programmer can decide not to register field modifications in a transactions. However,
        	 * he should be aware that he is not going to have control over those field changes.
        	 *                       -- marian
        	 */
        	/* Option 1:
        	$t =& $this->beginMemoryTransaction();
        	*/

        	/* Option 3:
        	print_backtrace_and_exit('Error: no active memory transaction to register modifications');
        	*/

        	/* Option 2: */
        	#@sql_echo echo 'Not registering ' . $mod->debugPrintString() . '(no memory transaction for: ' . $this->debugPrintString() .')</br>';@#
        }
        else {
	        $t->registerFieldModification($mod);
        }
    }

    function aboutToExecuteFunction(&$function) {
    	#@tm_echo2 echo 'Setting current component: ' . $this->debugPrintString() . '<br/>';@#
        defdyn('current_component', $this);
    }

    function functionExecuted(&$function) {
        #@tm_echo2 echo 'Unsetting current component: ' . $this->debugPrintString() . '<br/>';@#
        undefdyn('current_component');
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