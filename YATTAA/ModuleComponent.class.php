<?php

class ModuleComponent extends Component{
	function initialize(){
		$this->addComponent(new NavigationMenu,'navigationbar');
		$this->addComponent(new ActionsMenu,'actionsbar');
		$this->addComponent(new Component,'activeReference');
	}
	function changeBody(&$body) {
		$body->setModule($this);
		$this->addComponent($body, 'body');
		$cont =& $body->getContext();
		$this->showContext($cont);
	}
	function showContext(&$cont){
		$this->navigationbar->call($cont->getNavigationBar());
		$this->actionsbar->call($cont->getActionsBar());
	}
	function addReference(&$ref){
		$this->activeReference->addComponent(new CommandLink(array('text'=>$ref->getTitle(),'proceedFunction'=>new FunctionObject($ref, 'restoreContext'))),$ref->getInstanceId());
	}
	function removeReference(&$ref){
		$this->activeReference->deleteComponentAt($ref->getInstanceId());
	}
}

?>