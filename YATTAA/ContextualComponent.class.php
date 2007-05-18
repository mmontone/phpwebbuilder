<?php
/**
 * A Component with context.
 */
class ContextualComponent extends Component {
	function call(&$workspace){
		if ($this->__module!==null&&is_a($workspace,'ContextualComponent')){
			$this->workspaceCall($workspace);
		}
		parent::call($workspace);
	}
	function workspaceCall(&$workspace){
		$workspace->setModule($this->__module);
		$cont =& $this->getContext();
		$this->follower=&$workspace;
		$cont->switchTo($workspace->getContext());
	}
	function getTitle(){
		return getClass($this);
	}
	function restoreContext(){
		if(isset($this->follower)){
			$this->__module->removeReference($this->follower);
			$this->follower->restoreContext();
			$this->follower->callback();
			unset($this->follower);
		}
	}
	function restoreContextFromStart(){
		if(isset($this->follower)){
			$this->__module->removeReference($this->follower);
			$this->follower->restoreContextFromStart();
			unset($this->follower);
		}
	}
	function setModule(&$module){
		$this->__module=&$module;
		$module->addReference($this);
		$this->setDynVar('context', $this->newContext());
	}
	function &getContext(){
		return $this->getDynVar('context');
	}
    function start() {
        $context =& $this->getContext();
		$context->show();
		$this->restoreContextFromStart();
		$this->addAllMenus();
		$this->fillInActionsBar($context->getActionsBar());
		$this->fillInNavigationBar($context->getNavigationBar());
	}
	var $navigationmenus = array();
	function addNavigationMenu($name, $function){
		$this->navigationmenus[$name]=&$function;
	}
	var $actionmenus = array();
	function addActionMenu($name, $function){
		$this->actionmenus[$name]=&$function;
	}
	function removeActionMenu($name){
		unset($this->actionmenus[$name]);
		$ctx =& $this->getContext();
		$act =& $ctx->getActionBar();
		$act->deleteComponentAt($this->getInstanceId().$name);
	}
	function removeNavigationMenu($name){
		unset($this->navigationmenus[$name]);
		$ctx =& $this->getContext();
		$nav =& $ctx->getNavigationBar();
		$nav->deleteComponentAt($this->getInstanceId().$name);
	}
	function addAllMenus(){
		$ctx =& $this->getContext();
		$id = $this->getInstanceId();

		$nav =& $ctx->getNavigationBar();
		foreach($this->navigationmenus as $name=>$function){
			$nav->addComponent(new CommandLink(array('text' => $name, 'proceedFunction' => $function)), $id.$name);
		}

		$act =& $ctx->getActionsBar();
		foreach($this->actionmenus as $name=>$function){
			$act->addComponent(new CommandLink(array('text' => $name, 'proceedFunction' => $function)), $id.$name);
		}
	}
	function removeAllMenus(){
		$ctx =& $this->getContext();
		$id = $this->getInstanceId();

		$nav =& $ctx->getNavigationBar();
		foreach($this->navigationmenus as $name=>$function){
			$nav->deleteComponentAt($id.$name);
		}

		$act =& $ctx->getActionsBar();
		foreach($this->actionmenus as $name=>$function){
			$act->deleteComponentAt($id.$name);
		}
	}
	function stop(){
		$context =& $this->getContext();
		$this->removeAllMenus();
		$this->releaseActionsBar($context->getActionsBar());
		$this->releaseNavigationBar($context->getNavigationBar());
	}
	function & newContext() {
		$c =& new ContextMenus;
		return $c;
	}
	function fillInActionsBar(&$bar) {}
	function fillInNavigationBar(&$bar) {}
	function releaseActionsBar(&$bar) {}
	function releaseNavigationBar(&$bar) {}
}

?>