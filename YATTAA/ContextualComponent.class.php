<?php
/**
 * A Component with context.
 */
class ContextualComponent extends Component {
	var $__module;
	function call(&$workspace){
		$this->follower=&$workspace;
		if ($this->__module!==null && is_a($workspace,'ContextualComponent')){
			$this->workspaceCall($workspace);
		}
		parent::call($workspace);
	}
	function workspaceCall(&$workspace){
		$workspace->setModule($this->__module);
		$cont =& $this->getContext();
		$cont->switchTo($workspace->getContext());
	}
	function getTitle(){
		return getClass($this);
	}
	function restoreContext(){
		if(isset($this->follower)){
			if (is_a($this->follower,'ContextualComponent')){
				$this->__module->removeReference($this->follower);
				$this->follower->restoreContext();
			}
			$this->follower->callback();
			unset($this->follower);
		}
	}
	function restoreContextFromStart(){
		if(isset($this->follower)){
			if (is_a($this->follower,'ContextualComponent')){
				$this->__module->removeReference($this->follower);
				$this->follower->restoreContextFromStart();
			}
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
        if ($this->__module){
        	$context->show();
			$this->restoreContextFromStart();
        }
		$this->addAllMenus();
		$this->fillInActionsBar($context->getActionsBar());
		$this->fillInNavigationBar($context->getNavigationBar());
	}
	var $navigationmenus = array();
	function addNavigationMenu($name, &$function){
		$this->navigationmenus[$name]=&$function;
		$ctx =& $this->getContext();
		if ($ctx!==null){
			$nav =& $ctx->getNavigationBar();
			//$nav->addComponent(new CommandLink(array('text' => $name, 'proceedFunction' => &$function)), $this->getInstanceId().$name);
		}
	}
	var $actionmenus = array();
	function addActionMenu($name, &$function){
		$this->actionmenus[$name]=&$function;
		$ctx =& $this->getContext();
		if ($ctx!=null){
			$nav =& $ctx->getActionsBar();
			//$nav->addComponent(new CommandLink(array('text' => $name, 'proceedFunction' => &$function)), $this->getInstanceId().$name);
		}
	}
	function removeActionMenu($name){
		unset($this->actionmenus[$name]);
		$ctx =& $this->getContext();
		$act =& $ctx->getActionsBar();
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
		foreach(array_keys($this->navigationmenus) as $name1){
			$nav->addComponent(new CommandLink(array('text' => $name1, 'proceedFunction' => &$this->navigationmenus[$name1])), $id.$name1);
		}

		$act =& $ctx->getActionsBar();
		foreach(array_keys($this->actionmenus) as $name2){
			$act->addComponent(new CommandLink(array('text' => $name2, 'proceedFunction' => &$this->actionmenus[$name2])), $id.$name2);
		}
	}
	function removeAllMenus(){
		$ctx =& $this->getContext();
		$id = $this->getInstanceId();

		$nav =& $ctx->getNavigationBar();
		foreach(array_keys($this->navigationmenus) as $name){
			$nav->deleteComponentAt($id.$name);
		}

		$act =& $ctx->getActionsBar();
		foreach(array_keys($this->actionmenus) as $name){
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



class WrapperContextualComponent extends ContextualComponent{

	function WrapperContextualComponent(&$component){
		$this->comp =& $component;
		parent::ContextualComponent();
	}
	function initialize(){
		$this->addComponent($this->comp);
	}
}

?>