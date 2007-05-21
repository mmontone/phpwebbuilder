<?php

class ModuleComponent extends Component{
	function initialize(){
		$this->addComponent(new NavigationMenu,'navigationbar');
		$this->addComponent(new ActionsMenu,'actionsbar');
		$this->changeBody($this->getRootComponent());
	}
	function changeBody(&$body) {
		$this->resetReferences();
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
	function resetReferences(){
		$this->addComponent(new ReferenceBar,'activeReference');
	}
	function removeReference(&$ref){
		$this->activeReference->deleteComponentAt($ref->getInstanceId());
	}
}

?>