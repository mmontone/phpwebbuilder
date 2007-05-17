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
		$this->fillInActionsBar($context->getActionsBar());
		$this->fillInNavigationBar($context->getNavigationBar());
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