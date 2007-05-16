<?php

class ContextMenus {
	var $navigation_bar;
	var $actions_bar;
	function ContextMenus(){
		$this->setNavigationBar(new NavigationMenu);
		$this->setActionsBar(new ActionsMenu);
	}
	function &getNavigationBar() {
		return $this->navigation_bar;
	}

	function setNavigationBar(&$bar) {
		$this->navigation_bar =& $bar;
	}

	function setActionsBar(&$bar) {
		$this->actions_bar =& $bar;
	}

	function &getActionsBar() {
		return $this->actions_bar;
	}
	function releaseBars(){
		$this->actions_bar->callback();
		$this->navigation_bar->callback();
	}
	function switchTo(&$context){
		$this->actions_bar->call($context->actions_bar);
		$this->navigation_bar->call($context->navigation_bar);
		$this->follower =& $context;
	}
	function show(){
		if(isset($this->follower)){
			$this->follower->closeContext();
			unset($this->follower);
		}
	}
	function closeContext(){
		if(isset($this->follower)){
			$this->follower->closeContext();
			unset($this->follower);
		}
		$this->actions_bar->callback();
		$this->navigation_bar->callback();
	}
}

class ActionsMenu extends Component{}
class NavigationMenu extends Component{}

?>